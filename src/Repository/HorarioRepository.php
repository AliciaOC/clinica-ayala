<?php

namespace App\Repository;

use App\Entity\Horario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Horario>
 */
class HorarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Horario::class);
    }

//    /**
//     * @return Horario[] Returns an array of Horario objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Horario
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function borrarHorarioDeTerapeutaHuerfano(Horario $horario): void
    {
        if ($horario->getTerapeutas()->isEmpty()) {
            $this->getEntityManager()->getConnection()->createQueryBuilder()
                ->delete('terapeuta_horario')
                ->where('horario_id = :horarioId')
                ->setParameter('horarioId', $horario->getId())
                ->executeStatement();
        }else{
            $terapeutasIds = array_map(function($terapeuta) {
                return $terapeuta->getId();
            }, $horario->getTerapeutas()->toArray());
    
            //Es una consulta DBAL, no ORM
            $this->getEntityManager()->getConnection()->createQueryBuilder()
                ->delete('terapeuta_horario')
                ->where('terapeuta_id NOT IN (:terapeutasIds)')
                ->andWhere('horario_id = :horarioId')
                ->setParameter('terapeutasIds', $terapeutasIds, ArrayParameterType::INTEGER)//los arrays no son un tipo de dato nativo y no los entiende sin el ArrayParameterType
                ->setParameter('horarioId', $horario->getId())
                ->executeStatement();
        }
    }
}
