<?php

namespace App\Controller;

use App\Cache\PhoneCache;
use App\DTO\Phone\CreatePhone\CreatePhoneFromRequestInput;
use App\DTO\Phone\UpdatePhone\UpdatePhoneFromRequestInput;
use App\Entity\Phone;
use App\Helper\ViolationBuilder;
use App\Repository\PhoneRepository;
use App\Responder\JsonResponder;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as SecureSwag;
use OpenApi\Annotations as OA;

class PhoneController extends BaseEntityController
{
    /**
     * @var PhoneRepository
     */
    private $phoneRepository;
    /**
     * @var FilesystemAdapter
     */
    private $cache;
    /**
     * @var PhoneCache
     */
    private $phoneCache;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        PhoneRepository $phoneRepository,
        Security $security,
        PhoneCache $phoneCache
    ) {
        parent::__construct($serializer, $em, $validator, $security);
        $this->phoneRepository = $phoneRepository;
        $this->cache = new FilesystemAdapter();
        $this->phoneCache = $phoneCache;
    }

    //todo: add bad request

    /**
     * List all phones.
     *
     * <h1>Access for clients, users and admin</h1>
     *
     * @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *          type="object",
     *          @OA\Property (property="Phone",ref=@Model(type=Phone::class,groups={"list_phone"})),
     *          @OA\Property (
     *              property="_links",
     *              type="object",
     *              @OA\Property (property="self",type="string"),
     *          )
     *     )
     *  )
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT token not found || JWT token expired || Invalid JWT token",
     *
     *  )
     *
     * @OA\Parameter(
     *     name="HTTP_Authorization",
     *     in="header",
     *     description="Bearer {Token}",
     *     required= true,
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Parameter(
     *     name="page",
     *     in="path",
     *     description="Page number if we want to use pagination (start to 0)",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Parameter(
     *     name="brand",
     *     in="query",
     *     description="Search phones of one brand only",
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Parameter(
     *     name="model",
     *     in="query",
     *     description="Find a phone by model's name",
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Tag(name="Phone")
     * @SecureSwag(name="Bearer")
     *
     * @Route("/phones", name="list_phones", methods={"GET"})
     * @param JsonResponder $jsonResponder
     * @param Request $request
     * @return Response
     */
    public function allPhones(JsonResponder $jsonResponder, Request $request)
    {
        if ($request->query->get('model')) {
            $listJson = $this->phoneCache->allPhonesCache(
                'phones_json' . $request->query->get('model') . $_SERVER['APP_ENV'],
                3600,
                $request
            );
        } elseif ($request->query->get('brand')) {
            $listJson = $this->phoneCache->allPhonesCache(
                'phones_json' . $request->query->get('brand') . $_SERVER['APP_ENV'],
                3600,
                $request
            );
        } elseif ($request->query->get('page')) {
            $listJson = $this->phoneCache->allPhonesCache(
                'phones_json' . $request->query->get('page') . $_SERVER['APP_ENV'],
                3600,
                $request
            );
        } else {
            $listJson = $this->phoneCache->allPhonesCache(
                'all_phones_json' . $_SERVER['APP_ENV'],
                3600,
                $request
            );
        }

        return $jsonResponder::responder($listJson);
    }

    /**
     * One phone description
     *
     * <h1>Access for clients, users and admin</h1>
     *
     * @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *         @OA\Property ( property="Phone detail",ref=@Model(type=Phone::class, groups={"detail_phone"})),
     *         @OA\Property (
     *              property="_links",
     *              type="object",
     *              @OA\Property (property="update",type="string"),
     *              @OA\Property (property="delete",type="string")
     *          )
     *      )
     *  )
     *
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT token not found || JWT token expired || Invalid JWT token",
     *  )
     *
     * @OA\Response(
     *     response=404,
     *     description="NOT FOUND"
     *  )
     *
     * @OA\Parameter(
     *     name="HTTP_Authorization",
     *     in="header",
     *     description="Bearer {Token}",
     *     @OA\Schema(type="string"),
     *     required= true,
     * )
     *
     * @OA\Parameter (
     *     name="id",
     *     in="path",
     *     description="Phone's id",
     *     required=true,
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Tag(name="Phone")
     * @SecureSwag(name="Bearer")
     *
     * @Route("/phones/{id}",name="detail_phone",methods={"GET"})
     * @param Phone $phone
     * @return Response
     *
     */
    public function detailOnePhone(Phone $phone)
    {
        $phoneJson = $this->phoneCache->detailPhoneCache(
            'one_phone_json' . $phone->getId(),
            3600,
            $phone
        );

        return JsonResponder::responder($phoneJson);
    }

    /**
     * Create a phone
     *
     * <h1>Only Admin access</h1>
     *
     * @OA\Response(
     *     response=201,
     *     description="CREATED",
     *     @OA\Header(header="Location", description="Link to phone",
     *          @OA\Schema (
     *              type="string"
     *          )
     *      )
     *  )
     *
     * @OA\Response(
     *     response=400,
     *     description="BAD REQUEST"
     *  )
     *
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT token not found || JWT token expired || Invalid JWT token"
     *  )
     * @OA\Response(
     *     response=403,
     *     description="Forbidden"
     *  )
     *
     * @OA\Parameter(
     *     name="HTTP_Authorization",
     *     in = "header",
     *     description = "Bearer {Token}",
     *     required = true,
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\RequestBody  (
     *     required = true,
     *     description="Phone object that will be created",
     *     @OA\JsonContent(ref=@Model(type=Phone::class, groups={"detail_phone"}))
     * )
     *
     * @OA\Tag(name="Phone")
     * @SecureSwag(name="Bearer")
     *
     * @Route ("/phones", name="create_phone", methods={"POST"})
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function createPhone(Request $request)
    {
        /**
         * @var CreatePhoneFromRequestInput $phoneObject
         */
        $phoneObject = $this->serializer->deserialize(
            $request->getContent(),
            CreatePhoneFromRequestInput::class,
            'json'
        );

        $errors = $this->validator->validate($phoneObject);
        if ($errors->count() > 0) {
            $listErrors = ViolationBuilder::build($errors);
            return JsonResponder::responder(json_encode($listErrors), Response::HTTP_BAD_REQUEST);
        }

        $phone = Phone::createFromRequest($phoneObject);
        $this->em->persist($phone);
        $this->em->flush();
        return JsonResponder::responder(null, Response::HTTP_CREATED, ['Location' => "/api/phones/" . $phone->getId()]);
    }

    /**
     * Update a phone
     *
     * <h1>Only Admin access</h1>
     *
     * @OA\Response(
     *     response=204,
     *     description="NO CONTENT",
     *     @OA\Header(header="Location", description="Link to phone",@OA\Schema (type="string"))
     *  )
     *
     * @OA\Response(
     *     response=400,
     *     description="BAD REQUEST"
     *  )
     *
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT token not found || JWT token expired || Invalid JWT token"
     *  )
     *
     * @OA\Response(
     *     response=404,
     *     description="NOT FOUND"
     *  )
     *
     * @OA\Parameter(
     *     name="HTTP_Authorization",
     *     in = "header",
     *     description = "Bearer {Token}",
     *     required = true,
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Parameter (
     *    name = "id",
     *    in = "path",
     *    required = true,
     *    description="Phone's id"
     * )
     *
     * @OA\RequestBody  (
     *     required = true,
     *     description="Phone object that we will update",
     *     @OA\JsonContent(ref=@Model(type=Phone::class, groups={"detail_phone"}))
     * )
     *
     * @OA\Tag(name="Phone")
     * @SecureSwag(name="Bearer")
     *
     * @Route ("/phones/{id}",name="update_phone",methods={"PUT"})
     * @param Phone $phone
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function updatePhone(Phone $phone, Request $request)
    {
        $phoneDTO = new UpdatePhoneFromRequestInput();
        $phoneDTO->setId($phone->getId());
        $newPhone = $this->serializer->deserialize(
            $request->getContent(),
            UpdatePhoneFromRequestInput::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $phoneDTO]
        );

        $errors = $this->validator->validate($newPhone);
        if ($errors->count() > 0) {
            $listErrors = ViolationBuilder::build($errors);
            return JsonResponder::responder(json_encode($listErrors), Response::HTTP_BAD_REQUEST);
        }

        $phone->updateFromRequest($phoneDTO);
        $this->em->flush();

        return JsonResponder::responder(
            null,
            Response::HTTP_NO_CONTENT,
            ['Location' => '/api/phones/' . $phone->getId()]
        );
    }

    /**
     * Delete a phone
     *
     * <h1>Admin access only</h1>
     *
     * @OA\Response(
     *     response=204,
     *     description="NO CONTENT",
     *  )
     *
     * @OA\Response(
     *     response=401,
     *     description="UNAUTHORIZED - JWT token not found || JWT token expired || Invalid JWT token"
     *  )
     *
     * @OA\Response(
     *     response=404,
     *     description="NOT FOUND"
     *  )
     *
     * @OA\Parameter(
     *     name="HTTP_Authorization",
     *     in = "header",
     *     description = "Bearer {Token}",
     *     required = true,
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Parameter (
     *    name = "id",
     *    in = "path",
     *    required = true,
     *    description="Phone's id",
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Tag(name="Phone")
     * @SecureSwag(name="Bearer")
     *
     * @Route("/phones/{id}",name="delete_phone",methods={"DELETE"})
     * @param Phone $phone
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function deletePhone(Phone $phone)
    {
        $this->em->remove($phone);
        $this->em->flush();
        return JsonResponder::responder(null, Response::HTTP_NO_CONTENT);
    }
}
