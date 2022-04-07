<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin")
 */
class MainPageController extends AbstractController
{
    /**
     * @Route(methods={"GET"})
     */
    public function load(): Response
    {
        return $this->render('admin/main_page.html.twig', []);
    }
}
