<?php

namespace App\Importer;

use App\Entity\Programme;
use App\Exception\CustomException\InvalidPathToFileException;
use App\Repository\ProgrammeRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImportFromCSV
{
    private EntityManagerInterface $entityManager;

    private ProgrammeRepository $programmeRepository;

    private RoomRepository $roomRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProgrammeRepository $programmeRepository,
        RoomRepository $roomRepository
    ) {
        $this->entityManager = $entityManager;
        $this->programmeRepository = $programmeRepository;
        $this->roomRepository = $roomRepository;
    }

    /**
     * @throws InvalidPathToFileException
     * @throws \Doctrine\ORM\UnexpectedResultException
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

            $programme = new Programme();

            $programme->name = $column[0];
            $programme->description = $column[1];
            $programme->setStartTime(\DateTime::createFromFormat('d.m.Y H:i', $column[2]));
            $programme->setEndTime(\DateTime::createFromFormat('d.m.Y H:i', $column[3]));
            $programme->isOnline = \filter_var($column[4], FILTER_VALIDATE_BOOLEAN);
            $programme->maxParticipants = (int) $column[5];
            if (0 == \count($this->programmeRepository->getAll())) {
                $foundRoom = $this->roomRepository->findFirstRoom();
            }

            if (0 != \count($this->programmeRepository->getAll())) {
                $foundRoom = $this->roomRepository->findFirstAvailable($programme);
            }

            $programme->setRoom($foundRoom);

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
            ++$nr_imported;
        }

        \fclose($handler);
        \fclose($handlerMistakes);
    }
}
