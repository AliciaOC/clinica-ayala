<?php

namespace App\Repository;

use App\Entity\Cita;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cita>
 */
class CitaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cita::class);
    }

    //    /**
    //     * @return Cita[] Returns an array of Cita objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Cita
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findCitasPendientesByTerapeuta($terapeutaId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.terapeuta = :terapeutaId')
            ->andWhere('c.fecha >= :fecha')
            ->andWhere('c.estado = :estado')
            ->setParameter('terapeutaId', $terapeutaId)
            ->setParameter('fecha', new \DateTimeImmutable('now'))
            ->setParameter('estado', 'pendiente')
            ->orderBy('c.fecha', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCitasPasadasByTerapeuta($terapeutaId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.terapeuta = :terapeutaId')
            ->andWhere('c.fecha < :fecha')
            ->andWhere('c.estado != :estado')
            ->setParameter('terapeutaId', $terapeutaId)
            ->setParameter('fecha', new \DateTimeImmutable('now'))
            ->setParameter('estado', 'cancelada')
            ->orderBy('c.fecha', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function actualizarEstadoCitasPendientes($terapeutaId): void
    {
        $this->createQueryBuilder('c')
            ->update()
            ->set('c.estado', ':nuevoEstado')
            ->where('c.terapeuta = :terapeutaId')
            ->andWhere('c.fecha < :fecha')
            ->andWhere('c.estado = :estado')
            ->setParameter('terapeutaId', $terapeutaId)
            ->setParameter('fecha', new \DateTimeImmutable('now'))
            ->setParameter('estado', 'pendiente')
            ->setParameter('nuevoEstado', 'finalizada')
            ->getQuery()
            ->execute();
    }
}
