<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function searchByUserAndCriteria($user, ?string $q, ?string $status, ?string $category)
    {
        $qb = $this->createQueryBuilder('n')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user);

        if ($q) {
            $qb->andWhere('n.title LIKE :q OR n.content LIKE :q')
               ->setParameter('q', '%'.$q.'%');
        }

        if ($status) {
            $qb->andWhere('n.status = :status')
               ->setParameter('status', $status);
        }

        if ($category) {
            $qb->andWhere('n.category = :category')
               ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }
}
