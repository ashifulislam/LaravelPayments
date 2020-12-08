<?php

namespace App\Traits;

use GuzzleHttp\Client;
//To declare the trait including its' name
trait consumeExternalServices{
    public function makeRequest($method, $requestUrl, $queryParams=[], $formParams=[], $headers=[], $isJsonRequest=false){
     //client comes from guzzle http library
        $client =new Client(
            [
                //services or components that are using to use in this traits will be able to specify its own base
                //theory to calculate their routes for their request. So we need to specify that entry
                'base_uri'=>$this->baseUri,
            ]);
        //Add the capability here
        if(method_exists($this,'resolveAuthorization')){
            $this->resolveAuthorization($queryParams,$formParams,$headers);
        }

        //Based on the client's request we need to do get the response
        $response=$client->request($method,$requestUrl,[
            //options
            //If this is json request then it takes the 'FormParams' and transforms
            //into the json request if it is not then encoded correspond
            $isJsonRequest ? 'json':'form_params'=>$formParams,
            'headers'=>$headers,
            'queryParams'=>$queryParams,


            ]);
        //obtain the body and obtain the contain
        $response=$response->getBody()->getContents();

        if(method_exists($this,'decodeResponse')){
            //Decode response method
            $response=$this->decodeResponse($response);
        }


        return $response;
    }
}
?>
