<?php
namespace App\Services;

use App\Traits\consumeExternalServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PayPalService{
    //Send request to paypal through consumeExternalServices
    use consumeExternalServices;
    protected $baseUri;
    protected $clientId;
    protected $clientSecret;

    public function __construct(){
      $this->baseUri=config('services.paypal.base_uri');
      $this->clientId=config('services.paypal.client_id');
      $this->clientSecret=config('services.paypal.client_secret');
    }
    public function resolveAuthorization(&$queryParams,&$formParams,&$headers){
     $headers['Authorization']=$this->resolveAccessToken();
    }
    public function decodeResponse($response){
        return json_decode($response);
    }
    public function resolveAccessToken(){
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");
        return "Basic {$credentials}";
    }
    public function handlePayment(Request $request){
        //Order creation
        $order=$this->createOrder($request->value, $request->currency);

        //Obtaining set of links of that order. Array to collection(Transformation). Here $order->links is an array.
        //Order links are allowed us to search for only that link which their rel value is approved
        $orderLinks=collect($order->links);

        $approve=$orderLinks->where('rel','approve')->first();

        session()->put('approvalId',$order->id);
        //Regulate to href of that element
        return redirect($approve->href);


    }
    public function handleApproval(){
        if(session()->has('approvalId')){
            //Getting the approval id
            $approvalId=session()->get('approvalId');
            //Here is the payment object
            $payment=$this->capturePayment($approvalId);
            //All this stuff comes from the response
            $name=$payment->payer->name->given_name;
            $payment=$payment->purchase_units[0]->payments->captures[0]->amount;
            $amount=$payment->value;
            $currency=$payment->currency_code;
            return redirect()
                ->route('home')
                ->withSuccess(['payment'=>"Thanks,{$name}. We received your {$amount}{$currency} payment"]);


        }
       return redirect()
           ->route('home')
           ->withErrors('We can not capture the payment. Please jump again');
    }

    public function createOrder($value,$currency){
        return $this->makeRequest(
          'POST',
          '/v2/checkout/orders',
            //Query params
            [],

            [
                'intent'=>'CAPTURE',
                //Array with only the first position
                'purchase_units'=>[
                    0=>[
                        //object
                        'amount'=>[
                            'currency_code'=>strtoupper($currency),
                            'value'=>$value,
                        ]

                    ]
                ],
                'application_context'=>[
                    //To specify the brand name
                    'brand_name'=>config('app.name'),
                    'shipping_preference'=>'NO_SHIPPING',
                    'user_action'=>'PAY_NOW',
                    'return_url'=>route('approval'),
                    'cancel_url'=>route('cancelled')
                ]
            ],

            //Headers

            [],
            $isJsonRequest=true

        );
    }

    public function capturePayment($approvalId){
        return $this->makeRequest(
            'POST',
            "/v2/checkout/orders/{$approvalId}/capture",
            [],
            [],
            //header
            [
                //We add this header
                'Content-Type'=>'application/json'
            ],

        );
    }

}
?>
