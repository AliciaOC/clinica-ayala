<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findOneByEmail($email): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult() //devuelve un solo resultado o null
        ;
    }

    public function findOneById($id): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult() 
        ;
    }

    public function findByRole($role): array
    {
        $rsm = $this->createResultSetMappingBuilder('user');

        $query = sprintf(
            'SELECT %s
            FROM user user
            WHERE JSON_CONTAINS(user.roles, :role)
            ORDER BY user.id ASC',
            $rsm->generateSelectClause()
        );

        return $this->getEntityManager()
                ->createNativeQuery($query, $rsm)
                ->setParameter('role', $role)
                ->getResult();
      ;
    }

    //método para paginar
    public function findAllByRole($role, $pagina = 1, $elementosPorPagina = 10): array
    {
        $query = $this->createQueryBuilder('user')
            ->andWhere('user.roles = :val')
            ->setParameter('val', $role)
            ->orderBy('user.id', 'ASC')
            ->getQuery()
        ;

        $offset = ($pagina - 1) * $elementosPorPagina;//offset es el número de elementos que se saltan
        $query->setFirstResult($offset);
        $query->setMaxResults($elementosPorPagina);

        return $query->getResult();
    }

    public function borrar(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
}
