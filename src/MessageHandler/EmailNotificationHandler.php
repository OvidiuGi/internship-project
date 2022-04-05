<?php

namespace App\MessageHandler;

use App\Mailer\NewsletterMailer;
use App\Message\EmailNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EmailNotificationHandler implements MessageHandlerInterface
{
    private NewsletterMailer $mailer;

    public function __construct(NewsletterMailer $newsletterMailer)
    {
        $this->mailer = $newsletterMailer;
    }

    public function __invoke(EmailNotification $emailNotification)
    {
        $this->mailer->sendEmail($emailNotification->getReceiver(), $emailNotification->getBody());
    }
}
