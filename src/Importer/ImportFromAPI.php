<?php

namespace App\Importer;

use App\Decrypter\CaesarCipher;
use App\Entity\Programme;
use App\Exception\CustomException\EmptyAPIException;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImportFromAPI
{
    private CaesarCipher $decode;

    private RoomRepository $roomRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        CaesarCipher $decode,
        RoomRepository $roomRepository
    ) {
        $this->entityManager = $entityManager;
        $this->decode = $decode;
        $this->roomRepository = $roomRepository;
    }

    /**
     * @throws \Exception
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

            $programme = new Programme();
            $programme->name = $this->decode->decipher($line['name'], 8);
            $programme->description = $this->decode->decipher($line['description'], 8);
            $programme->setStartTime(\date_create_from_format('d.m.Y H:i', $line['startDate']));
            $programme->setEndTime(\date_create_from_format('d.m.Y H:i', $line['endDate']));
            $programme->setTrainer(null);
            $programme->isOnline = \filter_var($line['isOnline'], FILTER_VALIDATE_BOOLEAN);
            $programme->maxParticipants = $line['maxParticipants'];

            $foundRoom = $this->roomRepository->findFirstAvailable(
                $programme->getStartTime(),
                $programme->getEndTime(),
                $programme->maxParticipants,
                $programme->isOnline
            );
            $programme->setRoom($foundRoom);

            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }
    }
}
