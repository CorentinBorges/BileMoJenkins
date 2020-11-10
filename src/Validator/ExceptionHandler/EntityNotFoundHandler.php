<?php

namespace App\Validator\ExceptionHandler;

use App\Responder\ExceptionResponder\NotFoundJsonResponder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityNotFoundHandler
{
    public static function build(object $entity, string $message)
    {
        try {
            if ($entity == null) {
                throw new NotFoundHttpException($message);
            }
        } catch (NotFoundHttpException $exception) {
            NotFoundJsonResponder::build($exception);
        }
    }
}
