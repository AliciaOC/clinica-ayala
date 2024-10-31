<?php

namespace App\Repository;

use App\Entity\Terapeuta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Terapeuta>
 */
class TerapeutaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terapeuta::class);
    }

//    /**
//     * @return Terapeuta[] Returns an array of Terapeuta objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Terapeuta
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findByNombre($nombre): ?Terapeuta
    {
        return $this->createQueryBuilder('terapeuta')
            ->andWhere('terapeuta.nombre = :val')
            ->setParameter('val', $nombre)
            ->getQuery()
            ->getOneOrNullResult() //devuelve un solo resultado o null
        ;
    }

    public function getAllTerapeutas(): array
    {
        return $this->createQueryBuilder('terapeuta')
            ->getQuery()
            ->getResult();
    }
}
