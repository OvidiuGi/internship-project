<?php

namespace App\Controller;

use App\Form\ForgotMyPasswordType;
use App\Form\ResetPasswordType;
use App\Mailer\ResetPasswordMailer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    private EntityManagerInterface $entityManager;

    private ResetPasswordMailer $passwordMailer;

    private UserRepository $userRepository;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        ResetPasswordMailer $passwordMailer,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordMailer = $passwordMailer;
        $this->entityManager = $entityManager;
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

            if ($user) {
                $token = Uuid::v4();
                $user->forgotPasswordToken = $token;
                $user->setForgotPasswordTokenTime(new \DateTime('now'));

                $this->passwordMailer->sendEmail($givenEmail, $token);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        return $this->renderForm('forgot-password/forgot.password.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route(path="/reset-password", name="reset_password")
     */
    public function reset(Request $request): Response
    {
        $token = $request->query->all()['token'];

        $form = $this->createForm(ResetPasswordType::class);
        $user = $this->userRepository->findOneBy(['forgotPasswordToken' => $token]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user) {
                if (date_diff(new \DateTime('now'), $user->getForgotPasswordTokenTime())->i > 60) {
                    return new Response('The link expired', Response::HTTP_METHOD_NOT_ALLOWED);
                }

                $password = $form->getData()['password'];
                $user->setPassword($this->passwordHasher->hashPassword($user, $password));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        return $this->renderForm('forgot-password/forgot.password.html.twig', [
            'form' => $form,
        ]);
    }
}
