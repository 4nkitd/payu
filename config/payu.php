<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PayU Keys
    |--------------------------------------------------------------------------
    |
    */

    'key' => env('PAYU_KEY'),

    'secret' => env('PAYU_SECRET'),

    'payu_prod_endpoint' => env('PROD_ENDPOINT', 'https://test.payu.in/_payment'),
    'payu_test_endpoint' => env('TEST_ENDPOINT', 'https://secure.payu.in/_payment'),

    'payu_prod_api' => env('PROD_API_URI', 'https://info.payu.in/merchant/postservice.php?form=2'),
    'payu_test_api' => env('TEST_API_URI', 'https://test.payu.in/merchant/postservice.php?form=2'),

    'is_prod' => env('IS_PAYU_PROD', FALSE),


];
