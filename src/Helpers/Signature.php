<?php
namespace SHA443\BMLConnect\Helpers;

use SHA443\BMLConnect\Models\Transaction;

class Signature
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * Signature constructor.
     * @param Transaction $transaction
     * @param string $apiKey
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->apiKey = config('bml.BML_CLIENT_SECRET');
    }

    /**
     * @return string
     */
    public function sign()
    {
        $str = 'amount='.$this->transaction->getAmount().
            '&currency='.$this->transaction->getCurrency().
            '&apiKey='.$this->apiKey;

        return sha1($str);
    }

    // verifies the signaure of transaction and the signature provided by bml after payment

    /**
     * @return bool
     */
    public function verify($bml_signature)
    {
        if($this->sign()===$bml_signature)
        {
            return true;
        }

        return false;
    }
}
