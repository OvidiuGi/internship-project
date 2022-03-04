<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController
{
    /**
     * @Route("/message", methods={"GET"})
     */
    public function getMessage(): Response
    {
        return new JsonResponse("One Eternety Later...", Response::HTTP_OK, []);
    }

    /**
     * @Route("/message/create", methods={"POST"})
     */
    public function createMessage(Request $request): Response
    {
        return new JsonResponse($request->getContent(), Response::HTTP_OK, []);
    }
}
