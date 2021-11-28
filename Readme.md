# Dagar/Payments

if you want you can add it.

```php

<?php

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Dagar\PayU\PaymentGateway\PaymentGatewayContract', function ($app) {

            return new PayU('KEY', "SECRET", true);


        });
    }

```

your constructor and passing routes

```php

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dagar\PayU\PaymentGatewayContract;

class PayNowController extends Controller
{

    function __construct(PaymentGatewayContract $api)
    {
        $this->api = $api;
        $this->api->setURI(route('payments.success'),route('payments.failure'));
    }

    public function index()
    {

        $this->api->setDiscount(10);
        echo ($this->api->createOrder(100, 134562));

    }

}
```
