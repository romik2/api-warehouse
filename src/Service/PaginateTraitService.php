<?php

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;

trait PaginateTraitService
{
    public function paginate($dql, $page = 1, $limit = 5): Paginator
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
}