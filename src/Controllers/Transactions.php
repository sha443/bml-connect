<?php

namespace SHA443\BMLConnect\Controllers;

use SHA443\BMLConnect\Traits\Signature;
use SHA443\BMLConnect\Models\Transaction;

class Transactions
{
    const ENDPOINT = 'transactions';

    /**
     * @var Client
     */
    private $client;

    /**
     * Payments constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $json
     * @return mixed
     */
    public function create(array $json)
    {
        $transaction = (new Transaction())->fromArray($json);
        $json['signature'] = (new Signature($transaction))->sign();
        return $this->client->post(self::ENDPOINT, $json);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->client->get(self::ENDPOINT.'/'.$id);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function list(array $params)
    {
        return $this->client->get(self::ENDPOINT, $params);
    }
}
