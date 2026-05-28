<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // this header means that we can accept queries coming from localhost 5173
        header('Access-Control-Allow-Origin: http://localhost:5173');
        // this is to autorise http methods that vue.js can use
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        // header('Access-Control-Allow-Credentials: true');

        // ⚠️ IMPORTANT : répondre aux OPTIONS
        if ($request->getMethod() === 'options') {

            header('HTTP/1.1 200 OK');
            // http_response_code(200);
            exit;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
