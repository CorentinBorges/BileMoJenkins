<?php

namespace App\Cache;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Serializer\SerializerInterface;

class ClientCache
{

    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var FilesystemAdapter
     */
    private $cache;

    public function __construct(ClientRepository $clientRepository, SerializerInterface $serializer)
    {
        $this->clientRepository = $clientRepository;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter();
    }

    public function allClientCache(string $itemName, int $expiredAfter)
    {
        if (strpos($itemName, 'test') && $this->cache->hasItem($itemName)) {
            $this->cache->deleteItem($itemName);
        }

        /**
         * @var CacheItemInterface $element
         */
        $element = $this->cache->getItem($itemName);

        if (!$element->isHit()) {
            $all = $this->clientRepository->findAll();
            $dataToSet = $this->serializer->serialize($all, 'json', ['groups' => 'list_client']);
            $element->set($dataToSet);
            $element->expiresAfter($expiredAfter);
            $this->cache->save($element);
        }
        return $element->get();
    }

    public function clientDetailCache($itemName, int $expiredAfter, Client $client)
    {
        /**
         * @var CacheItemInterface $element
         */
        $element = $this->cache->getItem($itemName);

        if (!$element->isHit()) {
            $dataToSet = $this->serializer->serialize($client, 'json', ['groups' => 'client_details']);
            $element->set($dataToSet);
            $element->expiresAfter($expiredAfter);
            $this->cache->save($element);
        }
        return $element->get();
    }
}
