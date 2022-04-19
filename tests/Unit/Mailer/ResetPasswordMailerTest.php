<?php

namespace App\Tests\Unit\Mailer;

use App\Mailer\ResetPasswordMailer;
use Hoa\Iterator\Mock;
use Monolog\Test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\RouterInterface;

class ResetPasswordMailerTest extends TestCase
{
//    private MockObject $mailer;
//
//    private MockObject $logger;
//
//    private MockObject $router;
//
//    private ResetPasswordMailer $resetPasswordMailer;
//
//    public function setUp(): void
//    {
//        parent::setUp();
//
//        $this->router = $this->createMock(RouterInterface::class);
//
//        $this->mailer = $this->createMock(ResetPasswordMailer::class);
//
//        $this->logger = $this->createMock(LoggerAwareInterface::class);
//
//        $this->resetPasswordMailer = new ResetPasswordMailer($this->mailer, $this->router);
//    }
//    public function testSendEmail(): void
//    {
//        $this->mailer
//            ->expects($this->once())
//            ->method('sendEmail')
//            ->with('email@email.com', 'token');
//
//        $this->assert
//    }
}
