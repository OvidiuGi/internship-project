<?php

namespace App\Command;

use App\Command\CustomException\InvalidCSVRowException;
use App\Decrypter\CaesarCipher;
use App\Entity\Programme;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProgrammeImportFunction implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private EntityManagerInterface $entityManager;

    private CaesarCipher $decode;

    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        CaesarCipher $decode,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->decode = $decode;
        $this->validator = $validator;
    }

    /**
     * @throws InvalidCSVRowException
     */
    public function importFromCSV(
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
            $programme->assignDataToProgramme(
                $name,
                $description,
                $startTime,
                $endTime,
                $isOnline,
                $maxParticipants
            );
            $violationList = $this->validator->validate($programme);
            if ($violationList->count() > 0) {
                $message = 'Not able to import programme';
                $this->logger->warning($message);

                throw new \Exception($message);
            }
            $this->entityManager->persist($programme);
            $this->entityManager->flush();
            ++$nr_imported;
        }
    }

    public function importFromAPI(
        $data,
        int &$numberImported
    ): void {
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
            $violationList = $this->validator->validate($programme);
            if ($violationList->count() > 0) {
                $message = 'Not able to import programme';
                $this->logger->warning($message);

                throw new \Exception($message);
            }
            $this->entityManager->persist($programme);
            $this->entityManager->flush();
        }
    }
}
