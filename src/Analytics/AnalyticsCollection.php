<?php

namespace App\Analytics;

use App\Controller\Dto\AnalyticsDto;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @codeCoverageIgnore
 */
class AnalyticsCollection
{
    private ArrayCollection $apiLogins;

    private ArrayCollection $adminLogins;

    private ArrayCollection $newAccounts;

    private ArrayCollection $failedLogins;

    public function __construct()
    {
        $this->apiLogins = new ArrayCollection();

        $this->adminLogins = new ArrayCollection();

        $this->failedLogins = new ArrayCollection();

        $this->newAccounts = new ArrayCollection();
    }

    public function addToCollection(AnalyticsDto $dto): void
    {
        if ($dto->context['success'] === false) {
            $this->addFailedLogin($dto);

            return;
        }

        if ($dto->context['type'] === 'register') {
            $this->addNewAccount($dto);

            return;
        }

        if ($dto->context['role'] === 'ROLE_ADMIN') {
            $this->addAdminLogin($dto);
        }

        if ($dto->context['firewall'] === 'api') {
            $this->addApiLogin($dto);
        }
    }

    public function getAdminLogins(): ArrayCollection
    {
        return $this->adminLogins;
    }

    public function setAdminLogins(ArrayCollection $adminLogins): self
    {
        $this->adminLogins = $adminLogins;

        return $this;
    }

    public function addAdminLogin(AnalyticsDto $dto): self
    {
        if ($this->adminLogins->contains($dto)) {
            return $this;
        }

        $this->adminLogins->add($dto);

        return $this;
    }

    public function getApiLogins(): ArrayCollection
    {
        return $this->apiLogins;
    }

    public function setApiLogins(ArrayCollection $apiLogins): self
    {
        $this->apiLogins = $apiLogins;

        return $this;
    }

    public function addApiLogin(AnalyticsDto $dto): self
    {
        if ($this->apiLogins->contains($dto)) {
            return $this;
        }

        $this->apiLogins->add($dto);

        return $this;
    }

    public function getNumberAdminLoginsForDay(string $day): int
    {
        $day = \DateTime::createFromFormat('d.m.Y', $day);
        $day = $day->format('d.m.Y');
        $number = 0;
        foreach ($this->adminLogins as $adminLogin) {
            $dayFormat = $adminLogin->getDateTime()->format('d.m.Y');
            if ($dayFormat === $day) {
                $number++;
            }
        }

        return $number;
    }

    public function getNumberNewAccountsForRole(string $role): int
    {
        $number = 0;
        foreach ($this->newAccounts as $newAccount) {
            if ($newAccount->context['role'] === $role) {
                $number++;
            }
        }

        return $number;
    }

    public function getNumberApiLoginsForUsername(string $username): int
    {
        $number = 0;
        foreach ($this->apiLogins as $apiLogin) {
            if ($apiLogin->context['email'] === $username) {
                $number++;
            }
        }

        return $number;
    }

    public function getNewAccounts(): ArrayCollection
    {
        return $this->newAccounts;
    }

    public function setNewAccounts(ArrayCollection $newAccounts): self
    {
        $this->newAccounts = $newAccounts;

        return $this;
    }

    public function addNewAccount(AnalyticsDto $dto): self
    {
        if ($this->newAccounts->contains($dto)) {
            return $this;
        }

        $this->newAccounts->add($dto);

        return $this;
    }

    public function getFailedLogins(): ArrayCollection
    {
        return $this->failedLogins;
    }

    public function setFailedLogins(ArrayCollection $failedLogins): self
    {
        $this->failedLogins = $failedLogins;

        return $this;
    }

    public function addFailedLogin(AnalyticsDto $dto): self
    {
        if ($this->failedLogins->contains($dto)) {
            return $this;
        }

        $this->failedLogins->add($dto);

        return $this;
    }
}
