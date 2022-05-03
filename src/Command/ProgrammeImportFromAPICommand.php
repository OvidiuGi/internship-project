<?php

namespace App\Command;

use App\Exception\CustomException\EmptyAPIException;
use App\HttpClient\ImportProgrammeApiClient;
use App\Importer\ImportFromAPI;
use Doctrine\ORM\UnexpectedResultException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @codeCoverageIgnore
 */
class ProgrammeImportFromAPICommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $defaultName = 'app:programme:import-api';

    protected static $defaultDescription = 'Imports programmes from an external API.';

    private ImportProgrammeApiClient $client;

    private ImportFromAPI $import;

    public function __construct(ImportProgrammeApiClient $client, ImportFromAPI $import)
    {
        $this->client = $client;

        $this->import = $import;

        parent::__construct();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|UnexpectedResultException
     */
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
        if (count($data) > $numberImported) {
            $io->error($numberImported . ' / ' . count($data) . ' programmes imported!');
            $this->logger->error(
                'An error occurred while importing programmes!',
                [
                    'commandName' => self::$defaultName,
                    'numberImported' => $numberImported,
                    'totalProgrammes' => count($data)
                ]
            );

            return Command::FAILURE;
        }

        $io->success($numberImported . ' / ' . count($data) . ' programmes imported!');
        $this->logger->info(
            'Successfully imported programmes!',
            [
                'commandName' => self::$defaultName,
                'numberImported' => $numberImported,
                'totalProgrammes' => count($data)
            ]
        );

        return Command::SUCCESS;
    }
}
