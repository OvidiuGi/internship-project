<?php

namespace App\Controller\Admin;

use App\Form\UpdateUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin/users")
 */
class UserController extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private EntityManagerInterface $entityManager;

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(methods={"GET"}, name="users_page")
     */
    public function load(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userRepository->findAll();

        return $this->render('admin/main_page/users/users_page.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/update/{id}")
     */
    public function update(int $id, Request $request): Response
    {
        $form = $this->createForm(UpdateUserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $firstName = $form->getData()['firstname'];
            $lastName = $form->getData()['lastname'];
            $email = $form->getData()['email'];
            $telephoneNr = $form->getData()['telephoneNr'];
            $user = $this->userRepository->findOneBy(['id' => $id]);
            if (null === $user) {
                $this->logger->info('Cannot update user, it does not exist', ['id' => $id]);

                return $this->renderForm('admin/main_page/users/update.user.html.twig', [
                    'form' => $form,
                ]);
            }

            $user->firstName = $firstName;
            $user->lastName = $lastName;
            $user->email = $email;
            $user->telephoneNr = $telephoneNr;
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('users_page');
        }

        return $this->renderForm('admin/main_page/users/update.user.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}")
     */
    public function delete(int $id): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (null === $user) {
            var_dump('denied');
        }
        $this->userRepository->remove($user);

        return $this->redirectToRoute('main_page');
    }
}
