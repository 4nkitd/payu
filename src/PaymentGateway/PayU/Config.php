<?php

namespace Dagar\Payments\PaymentGateway\PayU;

class Config {

    private string $_TEST_URI = 'https://test.payu.in/_payment';
    private string $_PROD_URI = 'https://secure.payu.in/_payment';

    private string $_TEST_PAYMENT_STATUS = "https://test.payu.in/merchant/postservice.php?form=2";
    private string $_PROD_PAYMENT_STATUS = "https://info.payu.in/merchant/postservice.php?form=2";

    private bool $_SUCCESS_RETURN_URL;
    private bool $_FAILURE_RETURN_URL;

    private bool $_IS_PROD = True;

    private string $merchantId;
    private string $secretKey;

    private int $discount = 0;
    private int $fee = 0;

    public function setProdUri(string $uri){
        return $this->_TEST_URI = $uri;
    }

    public function setTestUri(string $uri){
        return $this->_TEST_URI = $uri;
    }

    public function setTestPaymentVerifyUri(string $uri){
        return $this->_TEST_PAYMENT_STATUS = $uri;
    }

    public function setProdPaymentVerifyUri(string $uri){
        return $this->_PROD_PAYMENT_STATUS = $uri;
    }

    public function isProd(bool $bool){
        return $this->_IS_PROD = $bool;
    }

    public function setMerchantKey(string $key){
        return $this->merchantId = $key;
    }

    public function setMerchantSecret(string $secret){
        return $this->secretKey = $secret;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    protected function calcFinalAMount($amount)
    {
        return $amount - $this->discount - $this->fee;
    }

    protected function getServiceUrl()
    {
        return $this->_TEST_MODE ? $this->_TEST_URI : $this->_PROD_URI;
    }

    protected function getPaymentStatusApiUrl()
    {
        return $this->_TEST_MODE ? $this->_TEST_PAYMENT_STATUS : $this->_PROD_PAYMENT_STATUS;
    }

    public function setURI(string $success, string $failure){
        $this->_SUCCESS_RETURN_URL = $success ;
        $this->_FAILURE_RETURN_URL = $failure ;
    }

}
