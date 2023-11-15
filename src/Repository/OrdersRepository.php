<?php

namespace App\Repository;

use App\Entity\Orders;
use App\Service\PaginateTraitService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Orders>
 *
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    use PaginateTraitService;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    public function add(Orders $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Orders $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function list(array $filters, int $currentPage, int $limit): Paginator
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.id', 'ASC');
        $this->applyFilters($qb, $filters);
        return $this->paginate($qb, $currentPage, $limit);
    }

    private function applyFilters(QueryBuilder &$qb, $filters = []): void
    {
        if (!empty($filters['createdAt'])) {
            $startAt = (new \DateTime($filters['createdAt']))->setTime(0,0);
            $endAt = (new \DateTime($filters['createdAt']))->setTime(23,59);
            $qb->andWhere($qb->expr()->between('o.createdAt', ':startCreateAt', ':endCreateAt'))
                ->setParameter('startCreateAt', $startAt)
                ->setParameter('endCreateAt', $endAt);
        }
    }
}
