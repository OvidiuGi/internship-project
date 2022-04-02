<?php

namespace App\Mailer;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Uid\Uuid;

class ResetPasswordMailer implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private MailerInterface $mailer;

    private RouterInterface $router;

    public function __construct(MailerInterface $mailer, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendEmail(string $givenEmail, Uuid $token): void
    {
        $changePasswordUrl = $this->router->generate(
            'reset_password',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->from('gireadaovidiu123@gmail.com')
            ->to($givenEmail)
            ->subject('OvidiuGym passowrd change request')
            ->text("We've received a password change request.\n This link will expire in 1 hour.")
            ->html("<a href=$changePasswordUrl>To change your password, click the link!</a>");

        $this->mailer->send($email);
    }
}
