<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function save(User $car, bool $flush = true): void
    {
        $this->getEntityManager()->persist($car);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
