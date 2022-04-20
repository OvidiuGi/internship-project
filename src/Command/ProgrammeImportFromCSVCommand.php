<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\CustomException\InvalidCSVRowException;
use App\Command\CustomException\InvalidPathToFileException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProgrammeImportFromCSVCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $defaultName = 'app:programme:import-csv';

    protected static $defaultDescription = 'Imports programmes from a CSV file.';

    private int $programmeMinTimeInMinutes;

    private int $programmeMaxTimeInMinutes;

    private string $handlerToImportFrom;

    private string $handlerToImportMistakes;

    private ProgrammeImport $import;

    public function __construct(
        string $programmeMinTimeInMinutes,
        string $programmeMaxTimeInMinutes,
        ProgrammeImport $import,
        string $handlerToImportFrom,
        string $handlerToImportMistakes
    ) {
        $this->programmeMaxTimeInMinutes = (int) $programmeMaxTimeInMinutes;
        $this->programmeMinTimeInMinutes = (int) $programmeMinTimeInMinutes;
        $this->import = $import;
        $this->handlerToImportFrom = $handlerToImportFrom;
        $this->handlerToImportMistakes = $handlerToImportMistakes;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $numberOfLines = 0;

        $numberImported = 0;

        try {
            $this->import->importFromCSV(
                $this->handlerToImportFrom,
                $this->handlerToImportMistakes,
                $numberImported,
                $numberOfLines
            );
        } catch (InvalidPathToFileException $e) {
            $this->logger->info($e->getMessage(), ['path' => \json_encode($e->getPathToFile())]);
            $io->error($e->getMessage());

            return Command::FAILURE;
        } catch (InvalidCSVRowException $exception) {
            $this->logger->info($exception->getMessage(), ['row' => \json_encode($exception->getRow())]);
            $io->error($exception->getMessage());

            return Command::FAILURE;
        } catch (NoResultException $noResultException) {
            $this->logger->info($noResultException->getMessage());
            $io->error($noResultException->getMessage());

            return Command::FAILURE;
        } finally {
            $io->info('Files closed succesfully!');
        }
        $io->info('Succesfully imported ' . $numberImported . ' / ' . $numberOfLines . ' programmes.');

        return Command::SUCCESS;
    }
}
