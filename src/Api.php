<?php

namespace Dagar\PayU\PaymentGateway;

use Dagar\PayU\Genesis;
use Dagar\PayU\Contracts\PaymentGatewayContract;
use Dagar\PayU\Resources\View;
use GuzzleHttp\Client;

class Api extends Genesis implements PaymentGatewayContract
{
    public function __construct($merchantId, $secretKey, $testMode = True)
    {

        parent::__construct();

        $this->merchantId = $merchantId;
        $this->secretKey = $secretKey;
        $this->_TEST_MODE = $testMode;

        $this->_view = new View();

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

        $output = $this->_view->load('form', []);

        $output .= sprintf('<form id="payment_form" method="POST" action="%s">', $this->getServiceUrl());

        foreach ($params as $key => $value) {
            $output .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value);
        }

        $output .= '<div id="redirect_info" style="display: none">Redirecting...</div>
                    <input id="payment_form_submit" type="submit" value="Proceed to PayUMoney" />
                </form>
                <script>
                    document.getElementById(\'redirect_info\').style.display = \'block\';
                    document.getElementById(\'payment_form_submit\').style.display = \'none\';
                    document.getElementById(\'payment_form\').submit();
                </script>';

        return $output;
    }

    public function verifyPaymentWithTxnID($transactionId)
    {
        $command = 'verify_payment';
        $hash_str = $this->merchantId . '|verify_payment|' . $transactionId . '|' . $this->secretKey;
        $hash = strtolower(hash('sha512', $hash_str));

        $client = new Client();

        return $client->request('POST', $this->getPaymentStatusApiUrl(), [
            'body' => [
                'key' => $this->merchantId,
                'hash' => $hash,
                'var1' => $transactionId,
                'command' => $command
            ]
        ]);
    }

}
