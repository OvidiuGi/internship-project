<?php

namespace App\Controller;

use App\Form\ForgotMyPasswordType;
use App\Form\ResetPasswordType;
use App\Mailer\ResetPasswordMailer;
use App\Repository\UserRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * @Route(path="/users")
 */
class ForgotMyPasswordController extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ResetPasswordMailer $passwordMailer;

    private UserRepository $userRepository;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ResetPasswordMailer $passwordMailer,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordMailer = $passwordMailer;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route(path="/forgot-password")
     */
    public function send(Request $request): Response
    {
        $form = $this->createForm(ForgotMyPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $givenEmail = $form->getData()['email'];

            $user = $this->userRepository->findOneBy(['email' => $givenEmail]);
            if (null === $user) {
                $this->logger->info('Email does not exist in database');

                return $this->renderForm('forgot-password/forgot.password.html.twig', [
                    'form' => $form,
                ]);
            }

            $token = Uuid::v4();
            $user->forgotPasswordToken = $token;
            $user->setForgotPasswordTokenTime(new \DateTime('now'));

            $this->passwordMailer->sendEmail($givenEmail, $token);

            $this->userRepository->add($user);
        }

        $this->logger->info('Password change request sent to ' . $givenEmail);

        return $this->renderForm('forgot-password/forgot.password.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route(path="/reset-password", name="reset_password")
     */
    public function reset(Request $request): Response
    {
        $user = $this->userRepository->findOneBy(['forgotPasswordToken' => $request->query->all()['token']]);
        if (null === $user) {
            $this->logger->info('No user found for token:' . $request->query->all()['token']);

            return new Response('The token is not valid', Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (date_diff(new \DateTime('now'), $user->getForgotPasswordTokenTime())->i > 60) {
                return new Response('The link expired', Response::HTTP_METHOD_NOT_ALLOWED);
            }

            $password = $form->getData()['password'];
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $this->userRepository->add($user);
        }
        $this->logger->info('Password changed for ' . $user->email);

        return $this->renderForm('forgot-password/forgot.password.html.twig', [
            'form' => $form,
        ]);
    }
}
