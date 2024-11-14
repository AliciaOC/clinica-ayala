<?php

namespace App\Repository;

use App\Entity\Tratamiento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Tratamiento>
 */
class TratamientoRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Tratamiento::class);
        $this->entityManager = $entityManager;
    }

    public function guardar(Tratamiento $tratamiento): void
    {
        $this->entityManager->persist($tratamiento);
        $this->entityManager->flush();
    }

    public function borrar(Tratamiento $tratamiento): void
    {
        $this->entityManager->remove($tratamiento);
        $this->entityManager->flush();
    }

    //Lo uso en editar tratamiento, para asegurarme que no pueda tener el mismo nombre que otro tratamiento tras la edición
    public function nombreDisponible($nombre, $id): ?Tratamiento
    {
        return $this->createQueryBuilder('tratamiento')
            ->andWhere('tratamiento.nombre = :nombre')
            ->andWhere('tratamiento.id != :id')
            ->setParameter('nombre', $nombre)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult() //devuelve un objeto o null
        ;
    }




    //    /**
    //     * @return Tratamiento[] Returns an array of Tratamiento objects
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

    public function findOneBySomeField($value): ?Tratamiento
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function seleccionarTratamientosTerapeutaNoTiene($terapeuta): array
    {
        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder();
    
        $result = $queryBuilder
            ->select('tratamiento.id, tratamiento.nombre')
            ->from('tratamiento', 'tratamiento')
            ->where('tratamiento.id NOT IN (SELECT tratamiento_id FROM tratamiento_terapeuta WHERE terapeuta_id = :terapeutaId)')
            ->setParameter('terapeutaId', $terapeuta->getId())
            ->executeQuery()
            ->fetchAllAssociative(); // Obtenemos el resultado como un array asociativo
    
        return $result;
    }
    
}
