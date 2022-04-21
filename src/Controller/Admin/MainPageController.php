<?php

namespace App\Controller\Admin;

use App\Analytics\LogParser;
use App\Repository\ProgrammeRepository;
use Doctrine\DBAL\Exception;
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
     * @throws Exception
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
        $newAccountsWithRole = [];
        foreach ($analytics->getNewAccounts() as $newAccount) {
            /** @var string $role */
            $role = $newAccount->context['role'];
            $newAccountsWithRole[$role] = $analytics->getNumberNewAccountsForRole($role);
        }

        return $this->render('admin/main_page/analytics/new_accounts.html.twig', [
            'newAccountsWithRole' => $newAccountsWithRole
        ]);
    }

    /**
     * @Route(path="/analytics/admin",methods={"GET"}, name="analytics_admin_logins")
     */
    public function getAdminLoginsAnalytics(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $analytics = $this->logParser->parseLogs();
        $adminLogins = [];
        foreach ($analytics->getAdminLogins() as $adminLogin) {
            $day = $adminLogin->getDateTime()->format('d.m.Y');
            $adminLogins[$day] = $analytics->getNumberAdminLoginsForDay($day);
        }

        return $this->render('admin/main_page/analytics/admin_logins.html.twig', [
            'adminLogins' => $adminLogins
        ]);
    }
}
