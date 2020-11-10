<?php

namespace App\Normalizers;

use App\Entity\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ClientNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;
    /**
     * @var Security
     */
    private $security;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer, Security $security)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->security = $security;
    }

    public function normalize($client, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($client, $format, $context);

        if (in_array('list_client', $context)) {
            $data['_link']['self'] = $this->router->generate('client_details', [
                'id' => $client->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        if (in_array('client_details', $context)) {
            if ($this->security->isGranted('admin_client')) {
                $data['_link']['update'] = $this->router->generate('update_client', [
                    'id' => $client->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);
                $data['_link']['delete'] = $this->router->generate('delete_client', [
                    'id' => $client->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            }
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Client;
    }
}
