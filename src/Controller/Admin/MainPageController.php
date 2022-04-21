<?php

namespace App\Controller\Admin;

use App\Analytics\LogParser;
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

    private LogParser $logParser;

    public function __construct(ProgrammeRepository $programmeRepository, LogParser $logParser)
    {
        $this->programmeRepository = $programmeRepository;
        $this->logParser = $logParser;
    }
    /**
     * @Route(methods={"GET"}, name="main_page")
     */
    public function load(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/main_page/main_page.html.twig', []);
    }

    /**
     * @Route(path="/reports/busiest-day",methods={"GET"}, name="busiest_day")
     */
    public function getBusiestDay(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $busiestDays = $this->programmeRepository->showBusiestDay();

        return $this->render('admin/main_page/reports/busiest_day.html.twig', [
            'busiestDays' => $busiestDays,
        ]);
    }

    /**
     * @Route(path="/analytics/accounts",methods={"GET"}, name="analytics_new_accounts")
     */
    public function getNewAccountsAnalytics(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $analytics = $this->logParser->parseLogs();
        $array = [];
        foreach ($analytics->getNewAccounts() as $newAccount) {

            /** @var string $role */
            $role = $newAccount->context['role'];
            $array[$role] = $analytics->getNumberNewAccountsForRole($role);
            $array = \array_unique($array);
        }

        return $this->render('admin/main_page/analytics/new_accounts.html.twig', [
        ]);
    }

//    /**
//     * @Route(path="/analytics/admin",methods={"GET"}, name="analytics_admin_logins")
//     */
//    public function getAdminLoginsAnalytics(): Response
//    {
//    }
}
