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

    private int $maxPerPage;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        int $maxPerPage
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->maxPerPage = $maxPerPage;
    }

    /**
     * @Route(methods={"GET"}, name="show_users")
     */
    public function showUsers(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $paginate = [];
        $paginate['page'] = $request->query->get('page', 1);
        $paginate['size'] = $request->query->get('size', 10);

        $users = $this->userRepository->getPaginated($paginate['page'], $paginate['size']);
        $totalPages = ceil(count($this->userRepository->findAll()) / $paginate['size']);

        return $this->render('admin/main_page/users/users_page.html.twig', [
            'users' => $users,
            'page' => $paginate['page'],
            'size' => $paginate['size'],
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * @Route(path="/update/{id}", methods={"GET","POST"}, name="update_user")
     */
    public function update(int $id, Request $request): Response
    {
        $form = $this->createForm(UpdateUserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(['id' => $id]);
            if (null === $user) {
                $this->logger->info('Cannot update user, it does not exist', ['id' => $id]);

                return $this->renderForm('admin/main_page/users/update.user.html.twig', [
                    'form' => $form,
                ]);
            }

            $user->firstName = $form->getData()['firstname'];
            $user->lastName = $form->getData()['lastname'];
            $user->email = $form->getData()['email'];
            $user->telephoneNr = $form->getData()['telephoneNr'];
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Succesffully edited user'
            );

            return $this->redirectToRoute('show_users');
        }

        return $this->renderForm('admin/main_page/users/update.user.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route(path="/delete/{id}",methods={"GET"}, name="delete_user")
     */
    public function delete(int $id): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (null === $user) {
            $this->addFlash(
                'error',
                'User does not exist',
            );
            return $this->redirectToRoute('show_users');
        }
        $this->userRepository->remove($user);
        $this->addFlash(
            'success',
            'Succesffully deleted user'
        );
        return $this->redirectToRoute('show_users');
    }
}
