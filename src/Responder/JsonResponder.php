<?php

namespace App\Responder;

use Symfony\Component\HttpFoundation\Response;

class JsonResponder
{
    public static function responder(?string $datas, int $statuCode = Response::HTTP_OK, array $headers = [])
    {
        return new Response($datas, $statuCode, array_merge(['Content-Type' => 'application/json'], $headers));
    }
}
