<?php
namespace SHA443\BMLConnect\Http\Controllers;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;

class Client
{
    const BML_API_VERSION = '2.0';
    const BML_APP_VERSION = 'sha443-bml-connect';
    const BML_SIGN_METHOD = 'sha1';
    const BML_SANDBOX_ENDPOINT = 'https://api.uat.merchants.bankofmaldives.com.mv/public/';
    const BML_PRODUCTION_ENDPOINT = 'https://api.merchants.bankofmaldives.com.mv/public/';

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var array
     */
    private $clientOptions;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var Transactions
     */
    public $transactions;


    /**
     * Client constructor.
     * @param string $apiKey
     * @param string $appId
     * @param string $mode
     * @param array $clientOptions
     */
    public function __construct($mode = 'production', array $clientOptions = [])
    {
        // read from config in laravel
        $this->apiKey = config("bml.BML_CLIENT_SECRET");
        $this->appId = config("bml.BML_CLIENT_ID");

        $this->mode = $mode;
        $this->baseUrl = ($mode === 'production' ? self::BML_PRODUCTION_ENDPOINT : self::BML_SANDBOX_ENDPOINT);
        $this->clientOptions = $clientOptions;

        $this->initiateHttpClient();

        $this->transactions = new Transactions($this);
    }

    /**
     * @param GuzzleClient $client
     */
    public function setClient(GuzzleClient $client)
    {
        $this->httpClient = $client;
    }

    /**
     * Initiates the HttpClient with required headers
     */
    private function initiateHttpClient()
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' =>  $this->apiKey,
            ]
        ];

        $this->httpClient = new GuzzleClient(array_replace_recursive($this->clientOptions, $options));
    }

    private function buildBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param Response $response
     * @return mixed
     */
    private function handleResponse(Response $response)
    {
        $stream = $response->getBody();
        $data = json_decode($stream);

        return $data;
    }

    /**
     * @param string $endpoint
     * @param array $json
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($endpoint, $json)
    {
        $json['apiVersion'] = self::BML_API_VERSION;
        $json['appVersion'] = self::BML_APP_VERSION;
        $json['signMethod'] = self::BML_SIGN_METHOD;

        $response = $this->httpClient->request('POST', $this->buildBaseUrl().$endpoint, ['json' => $json]);
        return $this->handleResponse($response);
    }

    /**
     * @param string $endpoint
     * @param array $pagination
     * @return mixed
     */
    public function get(string $endpoint, array $pagination = [])
    {
        $response = $this->httpClient->request(
            'GET',
            $this->applyPagination($this->buildBaseUrl().$endpoint, $pagination)
        );

        return $this->handleResponse($response);
    }

    /**
     * @param string $url
     * @param array $pagination
     * @return string
     */
    private function applyPagination(string $url, array $pagination)
    {
        if (count($pagination)) {
            return $url.'?'.http_build_query($this->cleanPagination($pagination));
        }

        return $url;
    }

    /**
     * @param array $pagination
     * @return array
     */
    private function cleanPagination(array $pagination)
    {
        $allowed = [
            'page',
        ];

        return array_intersect_key($pagination, array_flip($allowed));
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
