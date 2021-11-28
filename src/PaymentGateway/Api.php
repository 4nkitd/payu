<?php

namespace Dagar\PayU\PaymentGateway;

use Dagar\PayU\PaymentGateway\PaymentGatewayContract;
use Illuminate\Support\Facades\Http;

class Api extends Config implements PaymentGatewayContract
{
    public function __construct($merchantId, $secretKey, $testMode = False)
    {

        // parent::__construct();

        $this->merchantId = $merchantId;
        $this->secretKey = $secretKey;
        $this->_TEST_MODE = $testMode;
    }

    public function getChecksum(array $params)
    {
        $values = array_map(
            function ($field) use ($params) {
                return isset($params[$field]) ? $params[$field] : '';
            },
            $this->getChecksumParams()
        );

        $values = array_merge([$this->merchantId], $values, [$this->secretKey]);

        return hash('sha512', implode('|', $values));
    }

    private function getChecksumParams()
    {
        return array_merge(
            ['txnid', 'amount', 'productinfo', 'firstname', 'email'],
            array_map(function ($i) {
                return "udf{$i}";
            }, range(1, 10))
        );
    }

    public function createOrder($amount, $receiptId, $notes = [])
    {

        $params = [

            'key' => $this->merchantId,
            'txnid' => $receiptId,
            'amount' => $this->calcFinalAMount($amount),
            'firstname' => $notes["name"] ?? 'name',
            'email' => $notes["email"] ?? 'email@example.com',
            'phone' => $notes["phone"] ?? "9898989898",
            'surl' => $this->_SUCCESS_RETURN_URL,
            'furl' => $this->_FAILURE_RETURN_URL,
            'udf1' => 'discount='.$this->discount,
            'udf2' => 'discount='.$this->fee,

        ];

        unset($notes["name"]);
        unset($notes["email"]);
        unset($notes["phone"]);

        $params['productinfo'] = json_encode($notes);

        $params = array_merge($params, ['hash' => $this->getChecksum($params), 'key' => $this->merchantId]);
        $params = array_map(function ($param) {
            return htmlentities($param, ENT_QUOTES, 'UTF-8', false);
        }, $params);



        return view('payu::pay', [
            "params" => $params,
            'stripeKey' => config('cashier.key'),
            'uri' => $this->getServiceUrl(),
        ]);

    }

    public function verifyPaymentWithTxnID($transactionId)
    {
        $command = 'verify_payment';
        $hash_str = $this->merchantId . '|verify_payment|' . $transactionId . '|' . $this->secretKey;
        $hash = strtolower(hash('sha512', $hash_str));

        $response = Http::post( $this->getPaymentStatusApiUrl(),[
            'key' => $this->merchantId,
            'hash' => $hash,
            'var1' => $transactionId,
            'command' => $command
        ]);

        return json_decode($response, true);
    }

}
