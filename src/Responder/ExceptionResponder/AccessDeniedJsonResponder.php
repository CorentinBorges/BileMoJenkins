<?php

namespace App\Responder\ExceptionResponder;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessDeniedJsonResponder
{
    public static function build(AccessDeniedHttpException $exception)
    {
        echo json_encode(array("error" => [
            'Message' => "Access denied",
            'details' => $exception->getMessage()
        ]));
        http_response_code(403);
        header('Content-Type: application/json');
        exit;
    }
}
