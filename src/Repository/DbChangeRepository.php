<?php

namespace App\Repository;

use App\Entity\DbChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DbChange>
 *
 * @method DbChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method DbChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method DbChange[]    findAll()
 * @method DbChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DbChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DbChange::class);
    }

    public function add(DbChange $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DbChange $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByExampleField(): array
    {
        return $this->createQueryBuilder('d.id, d.created_at, d.table_name, d.entity_id, d.action, d.field_name, d.old_value, d.new_value, d.user_id')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?DbChange
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
