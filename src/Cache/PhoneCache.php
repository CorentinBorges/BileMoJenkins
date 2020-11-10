<?php

namespace App\Cache;

use App\Entity\Phone;
use App\Handlers\PhoneHandler;
use App\Repository\PhoneRepository;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class PhoneCache
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var PhoneRepository
     */
    private $phoneRepository;
    /**
     * @var FilesystemAdapter
     */
    private $cache;

    public function __construct(
        SerializerInterface $serializer,
        PhoneRepository $phoneRepository
    ) {
        $this->serializer = $serializer;
        $this->phoneRepository = $phoneRepository;
        $this->cache = new FilesystemAdapter();
    }

    public function allPhonesCache(string $itemName, int $expiredAfter, Request $request)
    {

        if (strpos($itemName, 'test') && $this->cache->hasItem($itemName)) {
            $this->cache->deleteItem($itemName);
        }
        /**
         * @var CacheItemInterface $element
         */
        $element = $this->cache->getItem($itemName);



        if (!$element->isHit()) {
            $listPhone = PhoneHandler::build($request, $this->phoneRepository);
            $dataToSet = $this->serializer->serialize($listPhone, 'json', ['groups' => 'list_phone']);
            $element->set($dataToSet);
            $element->expiresAfter($expiredAfter);
            $this->cache->save($element);
        }

        return $element->get();
    }

    public function detailPhoneCache($itemName, int $expiredAfter, Phone $phone)
    {
        /**
         * @var CacheItemInterface $element
         */
        $element = $this->cache->getItem($itemName);

        if (!$element->isHit()) {
            $dataToSet = $this->serializer->serialize($phone, 'json', ['groups' => "detail_phone"]);
            $element->set($dataToSet);
            $element->expiresAfter($expiredAfter);
            $this->cache->save($element);
        }
        return $element->get();
    }
}
