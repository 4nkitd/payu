<?php

namespace Dagar\PayU\Contracts;


interface PaymentGatewayContract
{

    public function setDiscount($discount);

    public function createOrder($amount, $receiptId, $notes = []);

}
