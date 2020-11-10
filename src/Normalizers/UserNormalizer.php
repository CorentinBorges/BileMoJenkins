<?php

namespace App\Normalizers;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($user, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($user, $format, $context);

        if (in_array('list_users', $context)) {
            $data['_link']['self'] = $this->router->generate('show_user_details', [
                'id' => $user->getClient()->getId(),
                'userId' => $user->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        if (in_array('user_details', $context)) {
            $data['_link']['update'] = $this->router->generate('update_user', [
                'id' => $user->getClient()->getId(),
                'userId' => $user->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $data['_link']['delete'] = $this->router->generate(
                'delete_user',
                [
                'id' => $user->getClient()->getId(),
                'userId' => $user->getId(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        // Here, add, edit, or delete some data:


        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof User;
    }
}
