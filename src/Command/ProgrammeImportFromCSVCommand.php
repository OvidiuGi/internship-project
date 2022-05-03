<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\CustomException\InvalidPathToFileException;
use App\Importer\ImportFromCSV;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\UnexpectedResultException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @codeCoverageIgnore
 */
class ProgrammeImportFromCSVCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $defaultName = 'app:programme:import-csv';

    protected static $defaultDescription = 'Imports programmes from a CSV file.';

    private string $handlerToImportFrom;

    private string $handlerToImportMistakes;

    private ImportFromCSV $import;

    public function __construct(
        ImportFromCSV $import,
        string $handlerToImportFrom,
        string $handlerToImportMistakes
    ) {
        $this->import = $import;

        $this->handlerToImportFrom = $handlerToImportFrom;

        $this->handlerToImportMistakes = $handlerToImportMistakes;

        parent::__construct();
    }

    /**
     * @throws UnexpectedResultException
     */
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
        } catch (NoResultException $noResultException) {
            $this->logger->info($noResultException->getMessage());
            $io->error($noResultException->getMessage());

            return Command::FAILURE;
        } finally {
            $io->info('Files closed successfully!');
        }

        $io->info('Successfully imported ' . $numberImported . ' / ' . $numberOfLines . ' programmes.');

        return Command::SUCCESS;
    }
}
