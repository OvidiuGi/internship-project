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

        echo $this->programmeMinTimeInMinutes . PHP_EOL;
        echo $this->programmeMaxTimeInMinutes . PHP_EOL;

        $arr = [];
        try {
            $pathHandler = __DIR__ . '/file.txt';
            $pathHandlerForMistakes = __DIR__ . '/fileWithBadData.txt';
            if (file_exists($pathHandler)) {
                $handler = fopen($pathHandler, 'r');
            } else {
                throw new InvalidPathToFileException('Invalid path to file', 0, null, $pathHandler);
            }
            if (file_exists($pathHandlerForMistakes)) {
                $handlerForMistakes = fopen($pathHandlerForMistakes, 'a+');
            } else {
                throw new InvalidPathToFileException('Invalid path to file', 0, null, $pathHandlerForMistakes);
            }

            $this->importFromCSV($handler, $handlerForMistakes);
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

        $io->success('Programmes imported!');

        return Command::SUCCESS;
    }

    private function importFromCSV($handler, $handlerForMistakes): void
    {
        fgetcsv($handler);
        while (($column = fgetcsv($handler, null, '|')) !== false) {
            if (sizeof($column) < 5) {
                fputcsv($handlerForMistakes, $column, '|');
                throw new InvalidCSVRowException('This row is not valid!', 0, null, $column);
            }
            $arr[] = $column;
        }

        foreach ($arr as $line) {
            $name = $line[0];
            $description = $line[1];
            $startTime = date_create_from_format('d.m.Y H:i', $line[2]);
            $endTime = date_create_from_format('d.m.Y H:i', $line[3]);
            $isOnline = filter_var($line[4], FILTER_VALIDATE_BOOLEAN);

            $programme = new Programme();
            $programme->name = $name;
            $programme->description = $description;
            $programme->setStartTime($startTime);
            $programme->setEndTime($endTime);
            $programme->isOnline = $isOnline;

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }
    }
}
