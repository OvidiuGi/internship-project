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

    private ProgrammeImportFunction $import;

    public function __construct(FetchFromApi $client, ProgrammeImportFunction $import)
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
            if (0 == count($data)) {
                throw new EmptyAPIException('API empty! Nothing to import!', 0, null, $data);
            }
        } catch (EmptyAPIException $apiException) {
            $this->logger->error('API empty! Nothing to import!');
            $io->error('API empty! Nothing to import!');

            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error('Programme not imported! Fix the issue and try again!');
            $this->logger->error('Programme not imported! Fix the issue and try again!');

            return Command::FAILURE;
        }
        if ($numberImported > 0) {
            $io->success($numberImported . ' / ' . count($data) . ' programmes imported!');
            $this->logger->info($numberImported . ' / ' . count($data) . ' programmes imported!');

            return Command::SUCCESS;
        }
        if ($numberImported == 0) {
            $io->error($numberImported . ' / ' . count($data) . ' programmes imported!');
            $this->logger->error($numberImported . ' / ' . count($data) . ' programmes imported!');

            return Command::FAILURE;
        }

        return Command::INVALID;
    }
}
