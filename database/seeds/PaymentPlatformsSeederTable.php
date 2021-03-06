<?php

use App\PaymentPlatform;
use Illuminate\Database\Seeder;

class PaymentPlatformsSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentPlatform::create([
            'name'=>'Paypal',
            'image'=>'img/payment-platforms/paypal.png',

        ]);

        PaymentPlatform::create([
            'name'=>'Stripe',
            'image'=>'img/payment-platforms/stripe.png',

        ]);
    }
}
