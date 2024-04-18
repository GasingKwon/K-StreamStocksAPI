<?php

namespace App\Filters;

use Config\Services;
use CodeIgniter\Filters\FilterInterface;

use CodeIgniter\HTTP\{
    RequestInterface,
    ResponseInterface
};

use Firebase\JWT\{
    JWT,
    Key
};

use CodeIgniter\API\ResponseTrait;

class AuthFilter implements FilterInterface {
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = null) {
        $uri = service('uri');
        $method = $uri->getSegment(2);

        $unchecked_methods = ['generateToken', 'swagger'];
        if(in_array($method, $unchecked_methods)) {
            return;
        }

        $authHeader = $request->getHeaderLine('Authorization');

        if($authHeader == false) {
            return Services::response()
            ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode([
                "status" => 401,
                "error" => 401,
                'message' => [
                    "error" => 'Header 정보가 누락되었습니다.'
                ]
            ]));
        }
        
        try {
            $accessToken = explode(" ", $authHeader)[1] ?? null;
            if (strlen($accessToken) < 1) {
                return Services::response()
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode([
                    "status" => 401,
                    "error" => 401,
                    'message' => [
                        "error" => '토큰 정보가 누락되었습니다.'
                    ],
                ]));
            }

            $decodedToken = JWT::decode($accessToken, new key(getenv('jwtKey'), 'HS256'));
            $request->access_token = $decodedToken->access_token;
        } catch (\Exception $e) {
            return Services::response()
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setHeader('Content-Type', 'application/json')
                ->setBody(json_encode([
                    "status" => 401,
                    "error" => 401,
                    'message' => [
                        "error" => '토큰 정보가 유효하지 않습니다.'
                    ],
                ]));                
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        
    }

    private function validateToken($token)
    {
        return true;
    }
}