<?php

namespace App\Validator\ExceptionHandler;

use App\Responder\ExceptionResponder\AccessDeniedJsonResponder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;

class AccessDeniedHandler
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function build(Security $security, string $votersAttribute, object $votersSubject, $message)
    {
        try {
            if (!$security->isGranted($votersAttribute, $votersSubject)) {
                throw new AccessDeniedHttpException($message);
            }
        } catch (AccessDeniedHttpException $exception) {
            AccessDeniedJsonResponder::build($exception);
        }
    }
}
