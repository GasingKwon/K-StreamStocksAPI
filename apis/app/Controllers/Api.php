<?php

namespace App\Controllers;

use Firebase\JWT\{
    JWT,
    Key
};

use CodeIgniter\API\ResponseTrait;

class Api extends BaseController {
    use ResponseTrait;

    public function test() {
        echo 'hello!';
        return;
    }

    public function generateToken()
    {
        $endpoint = '/oauth2/token';
        $apiURL = EBEST_API_URL.$endpoint;

        $request = array(
            'grant_type' => 'client_credentials',
            'appkey' => getenv('appkey'),
            'appsecretkey' => getenv('appsecretkey'),
            'scope' => 'oob',
        );

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $apiURL); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request)); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            $response = null;
        } 
        
        curl_close($ch);

        $resTokenInfo = json_decode($response, true);
        $accessToken = JWT::encode(['access_token' => $resTokenInfo['access_token']], getenv('jwtKey'), 'HS256');

        return $this->response
        ->setHeader('Authorization', "Bearer " . $accessToken)
        ->setStatusCode(200)
        ->setBody('Token issued');
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