<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Programme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProgrammeImportFromCSVCommand extends Command
{
    protected static $defaultName = 'app:programme:import-csv';

    protected static $defaultDescription = 'Imports programmes from a CSV file.';

    private int $programmeMinTimeInMinutes;

    private int $programmeMaxTimeInMinutes;

    private EntityManagerInterface $entityManager;

//    private ValidatorInterface $validator;

    public function __construct(
        string $programmeMinTimeInMinutes,
        string $programmeMaxTimeInMinutes,
        EntityManagerInterface $entityManager
        //        ValidatorInterface $validator
    ) {
        $this->programmeMaxTimeInMinutes = (int) $programmeMaxTimeInMinutes;
        $this->programmeMinTimeInMinutes = (int) $programmeMinTimeInMinutes;
        $this->entityManager = $entityManager;
//        $this->validator = $validator;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $totalNumberOfImportedLines = 0;
        $totalNumberOfLines = 0;

        echo $this->programmeMinTimeInMinutes.PHP_EOL;
        echo $this->programmeMaxTimeInMinutes.PHP_EOL;

        $arr = [];
        try {
            $pathHandler = '/home/govidiu/myproject/internship-project/src/FilesToImportFrom/file.txt';
            $handlerMistakes = '/home/govidiu/myproject/internship-project/src/FilesToImportFrom/fileWithBadData.txt';
            if (file_exists($pathHandler)) {
                $totalNumberOfLines = count(file($pathHandler)) - 1;
                $handler = fopen($pathHandler, 'r');
            } else {
                throw new InvalidPathToFileException('Invalid path to file', 0, null, $pathHandler);
            }
            if (file_exists($handlerMistakes)) {
                $handlerForMistakes = fopen($handlerMistakes, 'a+');
            } else {
                throw new InvalidPathToFileException('Invalid path to file', 0, null, $handlerMistakes);
            }

            $this->importFromCSV($handler, $handlerForMistakes, $totalNumberOfImportedLines);
        } catch (InvalidPathToFileException $e) {
            echo $e->getMessage();
            $io->error('Path to file not found! Fix the issue and try again!');
        } catch (InvalidCSVRowException $exception) {
            echo $exception->getMessage();
            $io->error('Programmes not imported! Fix the import file!');

            return Command::FAILURE;
        } finally {
            fclose($handler);
            fclose($handlerForMistakes);
            $io->info('Files closed succesfully!');
        }
        $io->success('Succesfully imported '.$totalNumberOfImportedLines.' / '.$totalNumberOfLines.' programmes.');

        return Command::SUCCESS;
    }

    private function importFromCSV(
        $handler,
        $handlerForMistakes,
        &$nr_imported
    ): void {
        fgetcsv($handler);
        while (($column = fgetcsv($handler, null, '|')) !== false) {
            if (sizeof($column) < 6) {
                fputcsv($handlerForMistakes, $column, '|');
                throw new InvalidCSVRowException('This row is not valid!', 0, null, $column);
            }
            $data[] = $column;
        }

        foreach ($data as $line) {
            $name = $line[0];
            $description = $line[1];
            $startTime = date_create_from_format('d.m.Y H:i', $line[2]);
            $endTime = date_create_from_format('d.m.Y H:i', $line[3]);
            $isOnline = filter_var($line[4], FILTER_VALIDATE_BOOLEAN);
            $maxParticipants = (int) $line[5];

            $programme = new Programme();
            $programme->name = $name;
            $programme->description = $description;
            $programme->setStartTime($startTime);
            $programme->setEndTime($endTime);
            $programme->isOnline = $isOnline;
            $programme->maxParticipants = $maxParticipants;

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
            ++$nr_imported;
        }
    }
}
