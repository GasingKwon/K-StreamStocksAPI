<?php

namespace App\Controllers;

class Login extends BaseController {

    public function test() {
        echo 'hello!';
        return;
    }

    public function getToken() {
        $endpoint = '/oauth2/token';
        $apiURL = EBEST_API_URL.$endpoint;

        $request = array(
            'grant_type' => 'client_credentials',
            'appkey' => getenv('appkey'),
            'appsecretkey' => getenv('appsecretkey'),
            'scope' => 'oob',
        );
        
        $res = $this->restfulCurl($request, $apiURL);
    }

    public function restfulCurl($req, $endpoint) {
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $endpoint); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req)); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            $response = null;
        } 
        
        curl_close($ch);

        print_r($response);
    }

}