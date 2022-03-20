<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\CustomException\InvalidCSVRowException;
use App\Command\CustomException\InvalidPathToFileException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProgrammeImportFromCSVCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $defaultName = 'app:programme:import-csv';

    protected static $defaultDescription = 'Imports programmes from a CSV file.';

    private int $programmeMinTimeInMinutes;

    private int $programmeMaxTimeInMinutes;

    private ProgrammeImportFunction $import;

    public function __construct(
        string $programmeMinTimeInMinutes,
        string $programmeMaxTimeInMinutes,
        ProgrammeImportFunction $import
    ) {
        $this->programmeMaxTimeInMinutes = (int) $programmeMaxTimeInMinutes;
        $this->programmeMinTimeInMinutes = (int) $programmeMinTimeInMinutes;
        $this->import = $import;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $numberOfLines = 0;

//        echo $this->programmeMinTimeInMinutes.PHP_EOL;
//        echo $this->programmeMaxTimeInMinutes.PHP_EOL;

        try {
            $pathHandler = '/home/govidiu/myproject/internship-project/src/FilesToImportFrom/file.txt';
            $handlerMistakes = '/home/govidiu/myproject/internship-project/src/FilesToImportFrom/fileWithBadData.txt';
            if (file_exists($pathHandler)) {
                $numberOfLines = count(file($pathHandler)) - 1;
                $handler = fopen($pathHandler, 'r');
            } else {
                throw new InvalidPathToFileException('Invalid path to file', 0, null, $pathHandler);
            }
            if (file_exists($handlerMistakes)) {
                $handlerForMistakes = fopen($handlerMistakes, 'a+');
            } else {
                throw new InvalidPathToFileException('Invalid path to file', 0, null, $handlerMistakes);
            }
            $this->import->importFromCSV($handler, $handlerForMistakes, $numberImported);
        } catch (InvalidPathToFileException $e) {
            echo $e->getMessage();
            $io->error('Path to file not found! Fix the issue and try again!');
        } catch (InvalidCSVRowException $exception) {
            echo $exception->getMessage();
            $io->error('Programmes not imported! Fix the import file!');

            return Command::FAILURE;
        } catch (\Exception $exp) {
            $this->logger->error('Not able to import programme');
            $io->error('Programme not imported!');
        } finally {
            fclose($handler);
            fclose($handlerForMistakes);
            $io->info('Files closed succesfully!');
        }
        $io->success('Succesfully imported ' . $numberImported . ' / ' . $numberOfLines . ' programmes.');

        return Command::SUCCESS;
    }
}
