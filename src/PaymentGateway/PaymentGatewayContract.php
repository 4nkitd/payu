<?php

namespace Dagar\PayU\PaymentGateway;


interface PaymentGatewayContract
{

    public function setDiscount($discount);

    public function createOrder($amount, $receiptId, $notes = []);

}
