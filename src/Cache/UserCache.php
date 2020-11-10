<?php

namespace App\Cache;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserCache
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var FilesystemAdapter
     */
    private $cache;

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter();
    }

    public function allUserCache(string $itemName, int $expiredAfter, Client $client)
    {
        /**
         * @var CacheItemInterface $element
         */
        $element = $this->cache->getItem($itemName);

        if (!$element->isHit()) {
            $usersList = $this->userRepository->findBy(['client' => $client]);
            $dataToSet = $this->serializer->serialize($usersList, 'json', ['groups' => 'list_users']);
            $element->set($dataToSet);
            $element->expiresAfter($expiredAfter);
            $this->cache->save($element);
        }
        return $element->get();
    }

    public function userDetailsCache(string $itemName, int $expiredAfter, User $user)
    {
        if (strpos($itemName, 'test') && $this->cache->hasItem($itemName)) {
            $this->cache->deleteItem($itemName);
        }

        /**
         * @var CacheItemInterface $element
         */
        $element = $this->cache->getItem($itemName);

        if (!$element->isHit()) {
            $dataToSet = $this->serializer->serialize(
                $user,
                'json',
                [
                    'groups' => 'user_details',
                    AbstractNormalizer::IGNORED_ATTRIBUTES => ['client'],
                ]
            );
            $element->set($dataToSet);
            $element->expiresAfter($expiredAfter);
            $this->cache->save($element);
        }
        return $element->get();
    }

    public function findUserCache(string $itemName, int $expiredAfter, string $userId)
    {
        /**
         * @var CacheItemInterface $element
         */
        $element = $this->cache->getItem($itemName);

        if (!$element->isHit()) {
            /**
             * @var User $user
             */
            $dataToSet = $this->userRepository->findOneBy(['id' => $userId]);
            $element->set($dataToSet);
            $element->expiresAfter($expiredAfter);
            $this->cache->save($element);
        }
        return $element->get();
    }
}
