<?php

namespace App\Command;

use App\HttpClient\FetchFromApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProgrammeImportFromAPICommand extends Command
{
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
        } catch (\Exception $e) {
            echo $e->getMessage();
            $io->error('Programme not imported! Fix the issue and try again!');
        }
        if ($numberImported > 0) {
            $io->success($numberImported . ' / ' . count($data) . ' programmes imported!');

            return Command::SUCCESS;
        }
        if ($numberImported < 0) {
            $io->error($numberImported . ' / ' . count($data) . ' programmes imported!');

            return Command::FAILURE;
        }

        return Command::INVALID;
    }
}
