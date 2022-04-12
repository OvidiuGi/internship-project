<?php

namespace App\Controller\Admin;

use App\Repository\ProgrammeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin")
 */
class MainPageController extends AbstractController
{
    private ProgrammeRepository $programmeRepository;

    public function __construct(ProgrammeRepository $programmeRepository)
    {
        $this->programmeRepository = $programmeRepository;
    }
    /**
     * @Route(methods={"GET"}, name="main_page")
     */
    public function load(): Response
    {


        return $this->render('admin/main_page/main_page.html.twig', []);
    }

    /**
     * @Route(path="/analitycs/busiest-day",methods={"GET"}, name="busiest_day")
     */
    public function getBusiestDay(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $arr = $this->programmeRepository->showBusiestDay();

        return $this->render('admin/main_page/analytics/busiest_day.html.twig', []);
    }
}
