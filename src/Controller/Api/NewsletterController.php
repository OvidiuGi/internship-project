<?php

namespace App\Controller\Api;

use App\Mailer\NewsletterMailer;
use App\Message\SmsNotification;
use App\Repository\UserRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="api/newsletter")
 */
class NewsletterController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
     * @Route(methods={"POST"}, name="api_newsletter")
     * @throws TransportExceptionInterface
     */
    public function sendNewsletterToAllAction(Request $request, MessageBusInterface $messageBus): Response
    {
        $body = $request->toArray()['body'];

        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->newsletterMailer->sendEmail($user->email, $body);

            $messageBus->dispatch(new SmsNotification($user->telephoneNr, $body));
        }
        return new JsonResponse('SMS and Email sent to all users', Response::HTTP_OK, [], true);
    }
}
