<?php
/*
Author: Shahid
Email: shahidcseku@gmail.com | shahidul.islam@villacollege.edu.mv
Date: 8 July 2021
Developed at: CICT, Villa College
*/

namespace SHA443\BMLConnect\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use SHA443\BMLConnect\Http\Controllers\Client;
use SHA443\BMLConnect\Http\Controllers\Transactions;

use SHA443\BMLConnect\Models\Transaction;

use SHA443\BMLConnect\Traits\Signature;

class BMLConnectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void 
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Create a new transaction
     *
     * @return void 
     */

    public function createTransaction($transaction_id=1)
    {
        $client = new Client('sandbox');

        // invoice data 
        $invoice_data = $this->getInovoiceData($transaction_id);

        // if you want to redirect after payment
        $invoice_data = array_merge($invoice_data, ["redirectUrl" => route("handleBMLResponse")]);

        $transaction = $client->transactions->create($invoice_data);
        // dd($transaction);
        return redirect($transaction->url); // Go to transaction payment page
    }

    /**
     * Handles bml post payment redirect event
     *
     * @return void 
     */
    public function handleResponse(Request $request)
    {
        $client = new Client('sandbox');

        $status = $request->input("state");
        $transaction_id = $request->input("transactionId");
        $bml_signature = $request->input("signature");

        // data retrived by transaction_id from bml merchant portal
        $transaction = $client->transactions->get($transaction_id);
        // dd($transaction); // See what you need from the response

        // retrive data from database using localId (your custom reference id)
        $invoice_data = $this->getInovoiceData($transaction->localId);

        // check if transaction is available in the system
        if(!$invoice_data)
        {
            return $this->showResponse("Transaction not found!");
        }

        // check status if confirmed
        if($status==="CONFIRMED")
        {
            // check signature for authentication 
            if($this->signatureCheck($invoice_data, $bml_signature))
            {
                return $this->generateReceipt($invoice_data, $status);
            }
            else
            {
                die("Signature mismatch, bad request!");
            }
        }
        else
        {
            return $this->showResponse($status);
        }
    }

    private function signatureCheck($invoice_data, $bml_signature)
    {
        // generate transaction;
        $transaction = (new Transaction())->fromArray($invoice_data);
        $verified = (new Signature($transaction))->verify($bml_signature);
        return $verified;
    }
    private function getInovoiceData($localId)
    {
        $invoice_data = [
            "currency" => "MVR", // currency
            "provider" => "bml_epos", // payment system
            "localId" => "Test_001", //(your custom reference id)
            "amount" => 1000, // 10.00 MVR
        ];

        return $invoice_data;
    }

    public function generateReceipt($invoice_data, $status)
    {
        // show your own style receipt
        return response(['status'=> $status, 'data'=> $invoice_data]);
    }

    public function showResponse($status)
    {
        // show your own style message
        return response(['status'=> $status]);
    }
}

?>