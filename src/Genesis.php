<?php

namespace Dagar\PayU;

class Genesis {

    protected string $_TEST_URI = 'https://secure.payu.in/_payment';
    protected string $_PROD_URI = 'https://test.payu.in/_payment';

    protected string $_TEST_PAYMENT_STATUS= 'https://test.payu.in/merchant/postservice.php?form=2';
    protected string $_PROD_PAYMENT_STATUS='https://info.payu.in/merchant/postservice.php?form=2';

    protected bool $_SUCCESS_RETURN_URL;
    protected bool $_FAILURE_RETURN_URL;

    protected bool $_IS_PROD = True;

    protected string $merchantId;
    protected string $secretKey;

    protected int $discount = 0;
    protected int $fee = 0;

    function __construct(){
        $this->_TEST_URI = getenv('PAYU_TEST_URI');
        $this->_PROD_URI = getenv('PAYU_PROD_URI');

        $this->_TEST_PAYMENT_STATUS = getenv('TEST_API_URI');
        $this->_PROD_PAYMENT_STATUS = getenv('PROD_API_URI');

        $this->_IS_PROD = getenv('IS_PAYU_PROD');
    }

    protected function _getViewLocation(){
        return __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
    }

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
        return $this->_IS_PROD ? $this->_TEST_URI : $this->_PROD_URI;
    }

    protected function getPaymentStatusApiUrl()
    {
        return $this->_IS_PROD ? $this->_TEST_PAYMENT_STATUS : $this->_PROD_PAYMENT_STATUS;
    }

    public function setURI(string $success, string $failure){
        $this->_SUCCESS_RETURN_URL = $success ;
        $this->_FAILURE_RETURN_URL = $failure ;
    }

}
