<?php

namespace App\Repository;

use App\Entity\Car;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function findAllCars(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByOwner(User|UserInterface $owner): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Car $car, bool $flush = true): void
    {
        $this->getEntityManager()->persist($car);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Car $car, bool $flush = true): void
    {
        $this->getEntityManager()->remove($car);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
