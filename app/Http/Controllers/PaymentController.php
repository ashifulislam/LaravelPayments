<?php

namespace App\Http\Controllers;


use App\Services\PayPalService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    //obtain a payment details
    public function pay(Request $request)
    {
       $rules=[
           'value'=>['required','numeric','min:5'],
           'currency'=>['exists:currencies,iso'],
           'payment_platform'=>['required','exists:payment_platforms,id'],

       ];
       $request->validate($rules);

       //To create new object for the paypal service
       $paymentPlatform=  resolve(PayPalService::class);

       return $paymentPlatform->handlePayment($request);

    }
    public function approval(){
        //To create new object for the paypal service

        $paymentPlatform=  resolve(PayPalService::class);

        return $paymentPlatform->handleApproval();

    }
    public function cancelled(){

    }
}
