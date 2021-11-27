<?php

namespace Dagar\Payments;

class Api {

    public static $VERSION = '1.0.0';
    private $merchantId;
    private $discount = 0;

    public function __construct($merchantId) {
        $this->merchantId = $merchantId;

    }

    public function setDiscount($discount) {
        $this->discount = $discount;
    }

    public function createOrder($amount, $receiptId, $notes = []){
        return [
            'amount' =>$amount - $this->discount,
            'discount' => $this->discount,
            'receiptId' => $receiptId,
            'orderId' => uniqid("pg_"),
            'notes' => $notes,
            'merchantId' => $this->merchantId
        ];
    }

}
