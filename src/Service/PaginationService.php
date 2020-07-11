<?php

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ObjectManager;

class PaginationService
{
    public function paginateResults(EntityRepository $repository, int $page, int $limit, ?string $criteria = null, $value = null)
    {
        if ($criteria && $value) {
            return $repository->findBy([$criteria => $value], [], $limit, ($page - 1) * $limit);
        }
        return $repository->findBy([], [], $limit, ($page - 1) * $limit);
    }

    public function getPages(EntityRepository $repository, int $limit, ?string $criteria = null, $value = null)
    {
        if ($criteria && $value) {
            return ceil(count($repository->findBy([$criteria => $value])) / $limit);
        }
        return ceil(count($repository->findAll()) / $limit);
    }
}