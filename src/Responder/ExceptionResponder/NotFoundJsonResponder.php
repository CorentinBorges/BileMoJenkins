<?php

namespace App\Responder\ExceptionResponder;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundJsonResponder
{
    public static function build(NotFoundHttpException $exception)
    {
        echo json_encode(array("error" => [
            'Message' => "Not found exception",
            'details' => $exception->getMessage()
        ]));
        http_response_code(404);
        header('Content-Type: application/json');
        exit;
    }
}
