<?php

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Programme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programme[]    findAll()
 */
class ProgrammeRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public const PROGRAMME_FIELDS_STRING = ['name', 'description'];

    public const PROGRAMME_FIELDS_INTEGER = ['id', 'isOnline', 'maxParticipants'];

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, Programme::class);
    }

    public function getAll(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p')
            ->getQuery()
            ->execute();
    }

    public function getPaginatedFilteredSorted(
        array $paginate,
        array $filters,
        ?string $sort,
        ?string $direction
    ): array {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p')
            ->setFirstResult($paginate['size'] * ($paginate['page'] - 1))
            ->setMaxResults($paginate['size']);

        foreach ($filters as $key => $value) {
            if (\in_array($key, self::PROGRAMME_FIELDS_STRING) && null != $value) {
                $query->andWhere("p.$key LIKE :value")->setParameter(':value', '%' . $value . '%');
            }

            if (\in_array($key, self::PROGRAMME_FIELDS_INTEGER) && null != $value) {
                $query->andWhere("p.$key = :value")->setParameter(':value', $value);
            }
        }
        $direction = strtoupper($direction);

        if (!\in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }

        if (\in_array($sort, self::PROGRAMME_FIELDS_STRING) && null != $sort) {
            $query->orderBy("p.$sort", $direction);
        }

        if (\in_array($sort, self::PROGRAMME_FIELDS_INTEGER) && null != $sort) {
            $query->orderBy("p.$sort", $direction);
        }

        return $query->getQuery()->getResult();
    }

    public function removeTrainerByIdFromProgrammes(int $id): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Programme', 'p')
            ->join('App\Entity\User', 'u')
            ->where('u.id = :givenId')
            ->setParameter('givenId', $id)
            ->getQuery()
            ->execute();
    }

    /**
     * @throws Exception
     */
    public function showBusiestDay(): array
    {
        $conn = $this->entityManager->getConnection();

        $sql = "
        SELECT p1.day,
            p1.hour,
            p1.participants
        FROM ( SELECT
            DATE_FORMAT(p.start_time, '%d-%m-%Y') as day,
            concat(DATE_FORMAT(p.start_time,'%H:%i'),' - ',DATE_FORMAT(p.end_time,'%H:%i')) as hour,
            count(pc.user_id) as participants,
            RANK() over(PARTITION BY DATE_FORMAT(p.start_time, '%d-%m-%Y') ORDER BY count(pc.user_id) desc) as position
        FROM programme p
        LEFT JOIN programmes_customers pc on p.id = pc.programme_id
        GROUP BY day, hour
        ) as p1
        WHERE p1.position = 1
        AND p1.participants > 0
        ORDER BY p1.participants desc
        LIMIT 5
        ";

        return $conn->prepare($sql)->executeQuery()->fetchAllAssociative();
    }
}
