<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotMyPasswordType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @Route(path="/users")
 */
class ForgotMyPasswordController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private RouterInterface $router;

    private MailerInterface $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        MailerInterface $mailer
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->mailer = $mailer;
    }

    /**
     * @Route(path="/forgot-password")
     */
    public function new(Request $request): Response
    {
        $task = new User();

        $form = $this->createForm(ForgotMyPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $givenEmail = $form->getData()['email'];
            $userRepository = $this->entityManager->getRepository(User::class);

            $user = $userRepository->findOneBy(['email' => $givenEmail]);

            if ($user) {
                $token = Uuid::v4();
                $user->forgotPasswordToken = $token;
                $user->setForgotPasswordTokenTime(new \DateTime());
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $newPasswordUrl = $this->router->generate(
                    'reset_password',
                    ['token' => $user->forgotPasswordToken],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $email = (new Email())
                    ->from('gireadaovidiu123@gmail.com')
                    ->to($givenEmail)
                    ->subject('Change password for account')
                    ->text('Click the link to change password')
                    ->html("<a href=$newPasswordUrl>Click me!</a>");

                $this->mailer->send($email);
            }
        }

        return $this->renderForm('task/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route(path="/reset-password", name="reset_password")
     */
    public function reset(Request $request)
    {
        $token = $request->query->all()['token'];

        $form = $this->createForm(ResetPasswordType::class);
        $userRepository = $this->entityManager->getRepository(User::class);

        $user = $userRepository->findOneBy(['forgotPasswordToken' => $token]);
        $form->handleRequest($request);
        if ($user) {
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $form->getData()['password'];
                $user->password = $password;
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }
        return $this->renderForm('task/new.html.twig', [
            'form' => $form,
        ]);
    }
}
