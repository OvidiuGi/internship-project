<?php

namespace App\Command;

use App\Command\CustomException\EmptyAPIException;
use App\Command\CustomException\InvalidCSVRowException;
use App\Command\CustomException\InvalidPathToFileException;
use App\Decrypter\CaesarCipher;
use App\Entity\Programme;
use App\Repository\ProgrammeRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProgrammeImport implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private EntityManagerInterface $entityManager;

    private CaesarCipher $decode;

    private RoomRepository $roomRepository;

    private ProgrammeRepository $programmeRepository;

    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        CaesarCipher $decode,
        ValidatorInterface $validator,
        RoomRepository $roomRepository,
        ProgrammeRepository $programmeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->decode = $decode;
        $this->validator = $validator;
        $this->roomRepository = $roomRepository;
        $this->programmeRepository = $programmeRepository;
    }

    /**
     * @throws InvalidCSVRowException
     * @throws InvalidPathToFileException
     */
    public function importFromCSV(
        $handler,
        $handlerMistakes,
        &$nr_imported,
        &$numberOfLines
    ): void {
        if (file_exists($handler)) {
            $numberOfLines = count(file($handler)) - 1;
            $handler = fopen($handler, 'r');
        } else {
            throw new InvalidPathToFileException('Invalid path to file', 0, null, $handler);
        }
        if (file_exists($handlerMistakes)) {
            $handlerMistakes = fopen($handlerMistakes, 'a+');
        } else {
            throw new InvalidPathToFileException('Invalid path to file', 0, null, $handlerMistakes);
        }
        fgetcsv($handler);
        while (($column = fgetcsv($handler, null, '|')) !== false) {
            if (sizeof($column) < 6) {
                fputcsv($handlerMistakes, $column, '|');
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
            $programme->assignDataToProgramme(
                $name,
                $description,
                $startTime,
                $endTime,
                $isOnline,
                $maxParticipants
            );

//            $programme->setRoom($this->roomRepository->assignRoom($startTime, $endTime, $maxParticipants, $isOnline));

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
            ++$nr_imported;

            fclose($handler);
            fclose($handlerMistakes);
        }
    }

    /**
     * @throws \Exception
     */
    public function importFromAPI(
        $data,
        int &$numberImported
    ): void {
        if (0 == count($data)) {
            throw new EmptyAPIException('API empty! Nothing to import!', 0, null);
        }
        foreach ($data as $line) {
            ++$numberImported;
            $name = $this->decode->decipher($line['name'], 8);
            $description = $this->decode->decipher($line['description'], 8);
            $startTime = date_create_from_format('d.m.Y H:i', $line['startDate']);
            $endTime = date_create_from_format('d.m.Y H:i', $line['endDate']);
            $isOnline = filter_var($line['isOnline'], FILTER_VALIDATE_BOOLEAN);
            $maxParticipants = $line['maxParticipants'];

            $programme = new Programme();
            $programme->assignDataToProgramme(
                $name,
                $description,
                $startTime,
                $endTime,
                $isOnline,
                $maxParticipants
            );

//            $programme->setRoom($this->roomRepository->assignRoom($startTime, $endTime, $maxParticipants, $isOnline));

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }
    }
}
