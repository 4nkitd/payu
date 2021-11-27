<?php

namespace Dagar\Payments\PaymentGateway\Razorpay;

use Dagar\Payments\PaymentGateway\PaymentGatewayContract;

class Razorpay implements PaymentGatewayContract
{

    private string $API = "https://api.razorpay.in/";

    private string $VERSION = "v1";

    private string $merchantKey;

    private string $merchantSecret;

    private int $discount = 0;
    private int $fee = 0;


    function __construct($merchantKey, $merchantSecret)
    {

        $this->merchantKey = $merchantKey;

        $this->merchantSecret = $merchantSecret;

    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    private function calcFinalAMount($amount)
    {
        return $amount - $this->discount - $this->fee;
    }

    protected function getServiceUrl()
    {
        return $this->API;
    }

    public function createOrder($amount, $receiptId, $notes = [])
    {
        return [
            'amount' =>$this->calcFinalAMount($amount),
            'discount' => $this->discount,
        ];
    }


}
