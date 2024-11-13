<?php

namespace App\Repository;

use App\Entity\Cliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cliente>
 */
class ClienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cliente::class);
    }

    //    /**
    //     * @return Cliente[] Returns an array of Cliente objects
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

    //    public function findOneBySomeField($value): ?Cliente
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function borrarClienteDeTerapeutaHuerfano(Cliente $cliente): void
    {
        $terapeutasIds = array_map(function($terapeuta) {
            return $terapeuta->getId();
        }, $cliente->getTerapeutas()->toArray());
    
        //Es una consulta DBAL, no ORM
        $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->delete('terapeuta_cliente')
            ->where('terapeuta_id NOT IN (:terapeutasIds)')
            ->andWhere('cliente_id = :clienteId')
            ->setParameter('terapeutasIds', $terapeutasIds, ArrayParameterType::INTEGER)//los arrays no son un tipo de dato nativo y no los entiende sin el ArrayParameterType
            ->setParameter('clienteId', $cliente->getId())
            ->executeStatement();
    }
}
