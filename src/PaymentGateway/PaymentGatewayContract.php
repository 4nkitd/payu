<?php

namespace Dagar\Payments\PaymentGateway;


interface PaymentGatewayContract
{

    public function setDiscount($discount);

    public function createOrder($amount, $receiptId, $notes = []);

}
