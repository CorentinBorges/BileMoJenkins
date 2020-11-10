<?php

namespace App\Handlers;

use App\Repository\PhoneRepository;
use Symfony\Component\HttpFoundation\Request;

class PhoneHandler
{
    public static function build(Request $request, PhoneRepository $phoneRepository)
    {
        if ($request->query->get('model')) {
            return $phoneRepository->findWithQuery(
                $request->query->get('page'),
                'model',
                $request->query->get('model')
            );
        } elseif ($request->query->get('brand')) {
            return $phoneRepository->findWithQuery(
                $request->query->get('page'),
                'brand',
                $request->query->get('brand')
            );
        } else {
            return $phoneRepository->findWithQuery($request->query->get('page'));
        }
    }
}
