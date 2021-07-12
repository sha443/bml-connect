
# BMLConnect
`sha443/bml-connect` is a laravel package for Bank of Maldives Connect API v2.0.


## Pre-requisites

- Requires PHP 7.3 or higher
- Laravel 5.5 or higher
- `production` or `sandbox` API key [get from [BML Merchant Portal](https://dashboard.merchants.bankofmaldives.com.mv/)]

## Installation & Setup
`composer require sha443/bml-connect`

Don't forget to run
`composer dump-autoload`

Set `BML_CLIENT_SECRET` and `BML_CLIENT_ID`in the `.env` file

## Quick Start

If you want to get a `sandbox` client:

```php
use SHA443\BMLConnect\Client;

$client = new Client("sandbox");
```

If you want to get a `production` client:

```php
use SHA443\BMLConnect\Client;

$client = new Client(); // passing 'production' is optional
//or
$client = new Client("production");
```
If you want to pass additional [GuzzleHTTP](https://github.com/guzzle/guzzle) options:
```php
use SHA443\BMLConnect\Client;

$options = ['headers' => ['foo' => 'bar']];
$client = new Client('sandbox', $options);
```
## Operations
### Available operations: 
- Create transaction with a specific payment method
- Create transaction without a specific payment method
- Get a transaction details
- List all transactions (with pagination)
- Verify signature

#### Create transaction with a specific payment method

```php
use SHA443\BMLConnect\Client;

$client = new Client();

$json = [
 "provider" => "bml_epos", // Payment method enabled for your merchant account such as bcmc, alipay, card, bml_epos
 "currency" => "MVR",
 "amount" => 1000, // 10.00 MVR, transaction amount should be an integer ([in Laari](https://en.wikipedia.org/wiki/Maldivian_laari))
 "localId" => "Test_001", // your reference id
 "redirectUrl" => "https://example.com/order/123" // Optional redirect after payment completion
];

$transaction = $client->transactions->create($json);
return redirect($transaction["url"]); // Go to transaction payment page
```
#### Create transaction without a payment method that will redirect to the payment method selection screen

```php
use SHA443\BMLConnect\Client;

$client = new Client();

$json = [
 "currency" => "MVR",
 "amount" => 1000, // 10.00 MVR, transaction amount should be an integer ([in Laari](https://en.wikipedia.org/wiki/Maldivian_laari))
 "localId" => "Test_001", // your reference id
 "redirectUrl" => "https://example.com/order/987" // Optional redirect after payment completion
];

$transaction = $client->transactions->create($json);
return redirect($transaction["url"]); // Go to transaction payment page
```

#### Verify a transaction signature locally
You'll get a response from BML as `transactionId=xxxxx&state=CONFIRMED&signature=xxx`
```php
use SHA443\BMLConnect\Client;
use SHA443\BMLConnect\Models\Transaction;
use SHA443\BMLConnect\Traits\Signature;

$client = new Client();

$json = [
 "currency" => "MVR",
 "amount" => 1000
];

$transaction = (new Transaction())->fromArray($json);
$verified = (new Signature($transaction))->verify($signature);
```

## About Me
Developer: [Shahidul Islam](https://github.com/sha443)
Email: shahidcseku@gmail.com, shahidul.islam@villacollege.edu.mv

*Any bug report/feature request is welcomed.*

#### Courtesy
This package is built on [bml-connect-php](https://github.com/bankofmaldives/bml-connect-php) but does not rely on it. It's a modified laravel friendly-package distributed under MIT License.
