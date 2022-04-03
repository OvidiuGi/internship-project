<?php

namespace App\Command;

use App\Command\CustomException\EmptyAPIException;
use App\HttpClient\FetchFromApi;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProgrammeImportFromAPICommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $defaultName = 'app:programme:import-api';

    protected static $defaultDescription = 'Imports programmes from an external API.';

    private FetchFromApi $client;

    private ProgrammeImport $import;

    public function __construct(FetchFromApi $client, ProgrammeImport $import)
    {
        $this->client = $client;
        $this->import = $import;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $data = $this->client->fetchData();
            $numberImported = 0;
            $this->import->importFromAPI($data, $numberImported);
        } catch (EmptyAPIException $apiException) {
            $this->logger->error($apiException->getMessage());
            $io->error($apiException->getMessage());

            return Command::FAILURE;
        }
        if (0 == $numberImported) {
            $io->error($numberImported . ' / ' . count($data) . ' programmes imported!');
            $this->logger->error($numberImported . ' / ' . count($data) . ' programmes imported!');

            return Command::FAILURE;
        }

        $io->success($numberImported . ' / ' . count($data) . ' programmes imported!');
        $this->logger->info($numberImported . ' / ' . count($data) . ' programmes imported!');

        return Command::SUCCESS;
    }
}
