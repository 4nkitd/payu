# Dagar/Payments

Example Implementation

```php

<?php

use Dagar\PayU\PaymentGateway\Api;

$inst = new Api(
    'KEY', # key provided by Team PayU
    "SECRET", # secret provided by Team PayU
    true # is test mode required
);

$bool = False; # True

$inst->isProd($bool);

$inst->setMerchantKey($string);
$inst->setMerchantSecret($string);

// $inst->setProdUri( $string ); # https://secure.payu.in/_payment
// $inst->setTestUri( $string ); # https://test.payu.in/_payment

// $inst->setTestPaymentVerifyUri( $string ); # https://test.payu.in/merchant/postservice.php?form=2
// $inst->setProdPaymentVerifyUri( $string ); # https://info.payu.in/merchant/postservice.php?form=2
// 

$inst->setDiscount( 10 );
$inst->setFee( 100 );

$inst->setURI($success_return_uri, $failure_return_uri);

$order = $inst->createOrder(
    $amount ?? 0 , 
    $receipt_No ?? random_int(1,999),
    [
        'name' => 'test', # name of client
        'email' => 'test', # name of client
        'phone' => 'test', # name of client
    ]
    ); 

```
