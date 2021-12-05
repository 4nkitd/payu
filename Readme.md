# Dagar/PayU

# Reason of Existance
Working with payment Gateways/aggregator's has become a common thing for me at office we work with almost all major payment gateway available in india. PayU, Razorpay & Stripe are at the top of our list. Razorpay  & Stripe has Phenomenally good sdks for all major languages/frameworks.

But when it comes to payu it's like they don't care about dev tooling.

i build this for people new to payu integration . So that they can follow the standard process and also be productive in 30-40 mins of reading this repository. 

## Future
- will add features along the way.

Note :-
    specifics can be found in issues

# FEATURES

    - Trigger payu redirect payment with ease.
    
    - Verify Payment Callback/Manually


## Import Package 
```php

use Dagar\PayU\PaymentGateway\Api;
```

## Create Class Instance
```php
$inst = new Api(
    'KEY', # key provided by Team PayU
    "SECRET", # secret provided by Team PayU
    true # is test mode required
);

```

## Set Return uri routes
```php

$inst->setURI($success_return_uri, $failure_return_uri);

```

## Change Parameters after creating the instance
```php

$inst->setMerchantKey($string);
$inst->setMerchantSecret($string);
$inst->isProd($bool);

$inst->setProdUri( $string ); 
# https://secure.payu.in/_payment
$inst->setTestUri( $string ); 
# https://test.payu.in/_payment

$inst->setTestPaymentVerifyUri( $string ); 
# https://test.payu.in/merchant/postservice.php?form=2
$inst->setProdPaymentVerifyUri( $string ); 
# https://info.payu.in/merchant/postservice.php?form=2
```

# Set Fee / Discount
```php

$inst->setDiscount( 10 );
$inst->setFee( 100 );

```

## Triger Payment

```php

$inst->createOrder(
    $amount ?? 0 , 
    $receipt_No ?? random_int(1,999),
    [
        'name' => 'test', # name of client
        'email' => 'test@test.test', # name of client
        'phone' => '9999999999', # name of client
    ]
); 

```

## Verify Callback Requests from PayU

```php

<?php


use Dagar\PayU\PaymentGateway\Api;

// reverse hash checking is always recommended for security reason and discarding invalid requests

$_RECEIVED_TXN_ID = $_POST['txnid'];

//  sensitize and verify that this txnId was send from your system

$inst = new Api(
    'KEY', # key provided by Team PayU
    "SECRET", # secret provided by Team PayU
    true # is test mode required
);

$resp = $inst->verifyPaymentWithTxnID($_RECEIVED_TXN_ID);

$resp['status'] // this is not the status of payment but the api response wether api request got executed correctly or not.

if ($resp['status']==1) {

    $txn_id = $resp['transaction_details'][$_RECEIVED_TXN_ID];
    // get info about transaction here you can get to know a lot about about the transaction it is recommended that you keep this response on your server somewhere

    $txn_id['net_amount_debit'];
    // this will tell you how much about was paid by the end customer

    $txn_id['status'] ?? $txn_id['unmappedstatus'];
    //  these will tell you payment was a success or not # remember to check for both

    //  update payment status

}

?>
