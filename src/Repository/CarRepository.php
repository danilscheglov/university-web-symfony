<?php

namespace App\Repository;

use App\Entity\Car;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findCarsByOwner(User $owner): array
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
        $this->_em->persist($car);

        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Car $car, bool $flush = true): void
    {
        $this->_em->remove($car);

        if ($flush) {
            $this->_em->flush();
        }
    }
}
