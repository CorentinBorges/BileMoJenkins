<?php

namespace App\Normalizers;

use App\Entity\Phone;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PhoneNormalizer implements ContextAwareNormalizerInterface
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

    public function normalize($phone, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($phone, $format, $context);

        if (in_array('list_phone', $context)) {
            $data['_link']['self'] = $this->router->generate('detail_phone', [
                'id' => $phone->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        if ($this->security->isGranted('admin_client')) {
            if (in_array('detail_phone', $context)) {
                $data['_link']['update'] = $this->router->generate('update_phone', [
                    'id' => $phone->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);
                $data['_link']['delete'] = $this->router->generate('delete_phone', [
                    'id' => $phone->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            }
        }
        // Here, add, edit, or delete some data:
        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Phone;
    }
}
