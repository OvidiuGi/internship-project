<?php

namespace App\Command;

use App\Entity\Building;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateBuildingCommand extends Command
{
    use LoggerAwareTrait;

    protected static $defaultName = 'app:create-building';

    protected static $defaultDescription = 'Creates a new building.';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('startTime', InputArgument::REQUIRED, 'Start Time.')
            ->addArgument('endTime', InputArgument::REQUIRED, 'End Time.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $startTime = $input->getArgument('startTime');
        $endTime = $input->getArgument('endTime');

        $building = new Building();

        $building->setStartTime(\date_create_from_format('H:i', $startTime));
        $building->setEndTime(\date_create_from_format('H:i', $endTime));

        $this->entityManager->persist($building);
        $this->entityManager->flush();

        $this->logger->info(
            'Successfully created building',
            [
                'buildingId' => $building->getId(),
                'commandName' => CreateBuildingCommand::$defaultName
            ]
        );

        $io->success('Building was successfully created!');

        return Command::SUCCESS;
    }
}
