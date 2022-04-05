<?php

namespace App\Controller;

use App\Mailer\NewsletterMailer;
use App\Message\EmailNotification;
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
     */
    public function sendAction(Request $request, MessageBusInterface $messageBus): Response
    {
        $this->logger->info('Starting action to send newsletter to user');

        $telephoneNr = $request->toArray()['receiver'];
        $body = $request->toArray()['body'];

        $user = $this->userRepository->findOneBy(['telephoneNr' => $telephoneNr]);
        if (null === $user) {
            $this->logger->warning('User not found with telephone number: '.$telephoneNr);

            return new JsonResponse('Not found', Response::HTTP_NOT_FOUND, [], true);
        }

        $emailMessage = new EmailNotification($user->email, $body);
        $messageBus->dispatch($emailMessage);

        $smsMessage = new SmsNotification($telephoneNr, $body);
        $messageBus->dispatch($smsMessage);

        $this->logger->info('The SMS and Email sent to user with email: '.$user->email);

        return new JsonResponse('SMS and Email sent', Response::HTTP_OK, [], true);
    }
}
