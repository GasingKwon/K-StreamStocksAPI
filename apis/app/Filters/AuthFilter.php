<?php

namespace App\Filters;

use Config\Services;
use CodeIgniter\Filters\FilterInterface;

use CodeIgniter\HTTP\{
    RequestInterface,
    ResponseInterface
};

use CodeIgniter\API\ResponseTrait;

class AuthFilter implements FilterInterface {
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null) {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !$this->validateToken($token)) {
            return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'Unauthorized or token expired');
        }

        $tokenData = $this->generateToken();

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        
    }

    private function validateToken($token)
    {
        $currentTime = time();
        $tokenExpirationTime = $currentTime + 3600;
        if ($currentTime <= $tokenExpirationTime) {
            return true;
        } else {
            return false; //토큰 만료
        }
    }

    private function generateToken()
    {
        $client = \Config\Services::curlrequest();

        $endpoint = '/oauth2/token';
        $apiURL = EBEST_API_URL.$endpoint;

        $request = array(
            'grant_type' => 'client_credentials',
            'appkey' => getenv('appkey'),
            'appsecretkey' => getenv('appsecretkey'),
            'scope' => 'oob',
        );

        try {
            $response = $client->post($apiURL, [
                'form_params' => $request,
                'headers' => [
                    'Content-Type: application/x-www-form-urlencoded',
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $body = $response->getBody();
                $data = json_decode($body, true);

                return [
                    'accessToken' => $data['accessToken'], // 응답에서 accessToken 필드를 추출
                    'expiresIn' => $data['expiresIn'], // 응답에서 expiresIn 필드를 추출
                ];
            }
        } catch (\Exception $e) {
            // 요청 실패 처리
            log_message('error', 'Token generation failed: '.$e->getMessage());
        }

        return null;
    }




}