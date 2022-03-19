<?php

namespace App\Command;

use App\Entity\Programme;
use App\Validator\CaesarCipher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProgrammeImportFromAPICommand extends Command
{
    protected static $defaultName = 'app:programme:import-api';

    protected static $defaultDescription = 'Imports programmes from an external API.';

    private HttpClientInterface $client;

    private EntityManagerInterface $entityManager;

    public function __construct(
        HttpClientInterface $client,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->client = $client;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $this->fetchGitHubInformation();

        $numberImported = 0;

        foreach ($data as $line) {
            ++$numberImported;
            $name = CaesarCipher::decipher($line['name'], 8);
            $description = CaesarCipher::decipher($line['description'], 8);
            $startTime = date_create_from_format('d.m.Y H:i', $line['startDate']);
            $endTime = date_create_from_format('d.m.Y H:i', $line['endDate']);
            $isOnline = filter_var($line['isOnline'], FILTER_VALIDATE_BOOLEAN);
            $maxParticipants = $line['maxParticipants'];

            $programme = new Programme();
            $programme->name = $name;
            $programme->description = $description;
            $programme->setStartTime($startTime);
            $programme->setEndTime($endTime);
            $programme->isOnline = $isOnline;
            $programme->maxParticipants = $maxParticipants;

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }

        $io->success($numberImported . ' / ' . count($data) . ' programmes imported!');

        return Command::SUCCESS;
    }

    public function fetchGitHubInformation(): array
    {
        $response = $this->client->request(
            'GET',
            'http://evozon-internship-data-wh.herokuapp.com/api/sport-programs'
        );
        $content = $response->getContent();
        $content = $response->toArray();
        $data = $content['data'];

        return $data;
    }
}
