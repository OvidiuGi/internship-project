<?php

namespace App\Controller;

use App\Mailer\NewsletterMailer;
use App\Message\SmsNotification;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="api/newsletter")
 */
class NewsletterController
{
    private UserRepository $userRepository;

    private NewsletterMailer $newsletterMailer;

    public function __construct(
        UserRepository $userRepository,
        NewsletterMailer $newsletterMailer
    ) {
        $this->userRepository = $userRepository;
        $this->newsletterMailer = $newsletterMailer;
    }

    /**
     * @Route(methods={"POST"})
     *
     * @throws TransportExceptionInterface
     */
    public function sendAction(Request $request, MessageBusInterface $messageBus): Response
    {
        $telephoneNr = $request->toArray()['receiver'];
        $body = $request->toArray()['body'];

        $user = $this->userRepository->findOneBy(['telephoneNr' => $telephoneNr]);
        if (null === $user) {
            return new JsonResponse('Not found', Response::HTTP_NOT_FOUND, [], true);
        }
        $this->newsletterMailer->sendEmail($user->email, $body);

        $message = new SmsNotification($telephoneNr, $body);
        $messageBus->dispatch($message);

        return new JsonResponse('SMS and Email sent', Response::HTTP_OK, [], true);
    }
}
