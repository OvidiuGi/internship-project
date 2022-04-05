<?php

namespace App\Controller;

use App\Mailer\NewsletterMailer;
use App\Message\SmsNotification;
use App\Repository\UserRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
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
     * @Route(methods={"POST"})
     *
     * @throws TransportExceptionInterface
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendNewsletterToOneAction(Request $request, MessageBusInterface $messageBus): Response
    {
        $telephoneNr = $request->toArray()['receiver'];
        $body = $request->toArray()['body'];

        $user = $this->userRepository->findOneBy(['telephoneNr' => $telephoneNr]);
        if (null === $user) {
            $this->logger->warning('User not found with telephone number: ' . $telephoneNr);

            return new JsonResponse('Not found', Response::HTTP_NOT_FOUND, [], true);
        }

        $this->newsletterMailer->sendEmail($user->email, $body);

        $messageBus->dispatch(new SmsNotification($telephoneNr, $body));

        return new JsonResponse('SMS and Email sent', Response::HTTP_OK, [], true);
    }

    /**
     * @Route(path="/all",methods={"POST"})
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
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
