<?php

namespace App\Repository;

use App\Entity\Terapeuta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
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

    public function borrarTerapeutaDeTratamientoHuerfano(Terapeuta $terapeuta): void
    {
        if ($terapeuta->getTratamientos()->isEmpty()) {
            $this->getEntityManager()->getConnection()->createQueryBuilder()
                ->delete('tratamiento_terapeuta')
                ->where('terapeuta_id = :terapeutaId')
                ->setParameter('terapeutaId', $terapeuta->getId())
                ->executeStatement();
        }else{
            $tratamientosIds = array_map(function($tratamiento) {
                return $tratamiento->getId();
            }, $terapeuta->getTratamientos()->toArray());
    
            //Es una consulta DBAL, no ORM
            $this->getEntityManager()->getConnection()->createQueryBuilder()
                ->delete('tratamiento_terapeuta')
                ->where('tratamiento_id NOT IN (:tratamientosIds)')
                ->andWhere('terapeuta_id = :terapeutaId')
                ->setParameter('tratamientosIds', $tratamientosIds, ArrayParameterType::INTEGER)//los arrays no son un tipo de dato nativo y no los entiende sin el ArrayParameterType
                ->setParameter('terapeutaId', $terapeuta->getId())
                ->executeStatement();
        }
    }
}
