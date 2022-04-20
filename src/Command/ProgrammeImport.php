<?php

namespace App\Command;

use App\Command\CustomException\EmptyAPIException;
use App\Command\CustomException\InvalidPathToFileException;
use App\Decrypter\CaesarCipher;
use App\Entity\Programme;
use App\Repository\ProgrammeRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnexpectedResultException;
use Exception;

class ProgrammeImport
{
    private EntityManagerInterface $entityManager;

    private CaesarCipher $decode;

    private RoomRepository $roomRepository;

    private ProgrammeRepository $programmeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CaesarCipher $decode,
        RoomRepository $roomRepository,
        ProgrammeRepository $programmeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->decode = $decode;
        $this->roomRepository = $roomRepository;
        $this->programmeRepository = $programmeRepository;
    }

    /**
     * @throws InvalidPathToFileException|UnexpectedResultException
     */
    public function importFromCSV(
        string $handler,
        string $handlerMistakes,
        int &$nr_imported,
        int &$numberOfLines
    ): void {
        if (!\file_exists($handler)) {
            throw new InvalidPathToFileException('Invalid path to file', 0, null, $handler);
        }

        if (!\file_exists($handlerMistakes)) {
            throw new InvalidPathToFileException('Invalid path to file', 0, null, $handlerMistakes);
        }

        $numberOfLines = \count(file($handler)) - 1;
        $handler = \fopen($handler, 'r');

        $handlerMistakes = \fopen($handlerMistakes, 'a+');

        \fgetcsv($handler);
        while (($column = \fgetcsv($handler, null, '|')) !== false) {
            if (\sizeof($column) < 6) {
                \fputcsv($handlerMistakes, $column, '|');

                continue;
            }

            $programme = Programme::createFromArray($column);
            if (0 == \count($this->programmeRepository->getAll())) {
                $foundRoom = $this->roomRepository->findFirstRoom();
            }

            if (0 != \count($this->programmeRepository->getAll())) {
                $foundRoom = $this->roomRepository->findFirstAvailable(
                    \DateTime::createFromFormat('d.m.Y H:i', $column[2]),
                    \DateTime::createFromFormat('d.m.Y H:i', $column[3]),
                    (int) $column[5],
                    \filter_var($column[4], FILTER_VALIDATE_BOOLEAN)
                );
            }

            $programme->setRoom($foundRoom);

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
            ++$nr_imported;
        }

        \fclose($handler);
        \fclose($handlerMistakes);
    }

    /**
     * @throws Exception
     */
    public function importFromAPI(
        array $data,
        int &$numberImported
    ): void {
        if (0 == \count($data)) {
            throw new EmptyAPIException('API empty! Nothing to import!', 0, null);
        }

        foreach ($data as $line) {
            ++$numberImported;
            $name = $this->decode->decipher($line['name'], 8);
            $description = $this->decode->decipher($line['description'], 8);
            $startTime = \date_create_from_format('d.m.Y H:i', $line['startDate']);
            $endTime = \date_create_from_format('d.m.Y H:i', $line['endDate']);
            $isOnline = \filter_var($line['isOnline'], FILTER_VALIDATE_BOOLEAN);
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

            $foundRoom = $this->roomRepository->findFirstAvailable($startTime, $endTime, $maxParticipants, $isOnline);
            $programme->setRoom($foundRoom);

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }
    }
}
