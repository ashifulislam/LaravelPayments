<?php
namespace App\Services;

use App\Traits\consumeExternalServices;

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
