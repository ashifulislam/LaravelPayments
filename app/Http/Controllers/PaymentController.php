<?php

namespace App\Http\Controllers;


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
       return $request->all();
    }
    public function approval(){

    }
    public function cancelled(){

    }
}
