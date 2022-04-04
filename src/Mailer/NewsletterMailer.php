<?php

namespace App\Mailer;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NewsletterMailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $givenEmail, string $message): void
    {
        $email = (new Email())
            ->from('gireadaovidiu123@gmail.com')
            ->to($givenEmail)
            ->subject('Newsletter')
            ->text($message);

        $this->mailer->send($email);
    }
}
