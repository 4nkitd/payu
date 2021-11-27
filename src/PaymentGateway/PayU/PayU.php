<?php

namespace Dagar\Payments\PaymentGateway\PayU;

use Dagar\Payments\PaymentGateway\PaymentGatewayContract;
use Illuminate\Support\Facades\Http;

class PayU extends Config implements PaymentGatewayContract
{
    public function __construct($merchantId, $secretKey, $testMode = False)
    {
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

    private function generatePaymentPageDOM(){
        return '
                <input id="payment_form_submit" type="submit" value="Proceed to PayUMoney" />

                </form>

                <script>
                    document.getElementById(\'redirect_info\').style.display = \'block\';
                    document.getElementById(\'payment_form_submit\').style.display = \'none\';
                    document.getElementById(\'payment_form\').submit();
                </script>
        ';
    }

    private function generateCssLoader() {
        return '
            <style>

            .lds-spinner {
                color: official;
                display: inline-block;
                position: relative;
                width: 80px;
                height: 80px;
              }
              .lds-spinner div {
                transform-origin: 40px 40px;
                animation: lds-spinner 1.2s linear infinite;
              }
              .lds-spinner div:after {
                content: " ";
                display: block;
                position: absolute;
                top: 3px;
                left: 37px;
                width: 6px;
                height: 18px;
                border-radius: 20%;
                background: #000;
              }
              .lds-spinner div:nth-child(1) {
                transform: rotate(0deg);
                animation-delay: -1.1s;
              }
              .lds-spinner div:nth-child(2) {
                transform: rotate(30deg);
                animation-delay: -1s;
              }
              .lds-spinner div:nth-child(3) {
                transform: rotate(60deg);
                animation-delay: -0.9s;
              }
              .lds-spinner div:nth-child(4) {
                transform: rotate(90deg);
                animation-delay: -0.8s;
              }
              .lds-spinner div:nth-child(5) {
                transform: rotate(120deg);
                animation-delay: -0.7s;
              }
              .lds-spinner div:nth-child(6) {
                transform: rotate(150deg);
                animation-delay: -0.6s;
              }
              .lds-spinner div:nth-child(7) {
                transform: rotate(180deg);
                animation-delay: -0.5s;
              }
              .lds-spinner div:nth-child(8) {
                transform: rotate(210deg);
                animation-delay: -0.4s;
              }
              .lds-spinner div:nth-child(9) {
                transform: rotate(240deg);
                animation-delay: -0.3s;
              }
              .lds-spinner div:nth-child(10) {
                transform: rotate(270deg);
                animation-delay: -0.2s;
              }
              .lds-spinner div:nth-child(11) {
                transform: rotate(300deg);
                animation-delay: -0.1s;
              }
              .lds-spinner div:nth-child(12) {
                transform: rotate(330deg);
                animation-delay: 0s;
              }
              @keyframes lds-spinner {
                0% {
                  opacity: 1;
                }
                100% {
                  opacity: 0;
                }
              }

            </style>

            <center style="margin-top: 40vh;">
            <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
            <div id="redirect_info" style="display: none; margin-top: 20px;">Redirecting...</div>
            <center>
        ';
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

        $output = $this->generateCssLoader();
        $output .= sprintf('<form id="payment_form" method="POST" action="%s">', $this->getServiceUrl());

        foreach ($params as $key => $value) {
            $output .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value);
        }

        $output .= $this->generatePaymentPageDOM();

        return $output;
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
