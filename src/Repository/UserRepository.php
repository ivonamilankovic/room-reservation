<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserInMeeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

     /**
      * @return User[]
      */

    public function findUsersForMeeting($loggedUser)
    {
        //daje sve korisnike sem onog koji zeli da kreira sastanak
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :val')
            ->setParameter('val', $loggedUser)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return User[]
     */
    public function findUsersOnMeeting($meeting_id)
    {
        //daje korisnike da datom sastanku
        return $this->createQueryBuilder('u')
            ->leftJoin(UserInMeeting::class, 'uim', Join::WITH, 'uim.user = u.id')
            ->andWhere('uim.meeting = :val')
            ->setParameter('val', $meeting_id)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneChiefOfSector($sectorID)
    {
        $role = 'ROLE_CHIEF';
        // The ResultSetMapping maps the SQL result to entities
        $rsm = $this->createResultSetMappingBuilder('u');

        $rawQuery = sprintf(
            'SELECT %s
        FROM user u 
        WHERE JSON_CONTAINS(u.roles, :role, \'$\') AND u.sector_id = :sectorID',
            $rsm->generateSelectClause()
        );

        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
        $query->setParameter('role',sprintf('"%s"', $role));
        $query->setParameter('sectorID', $sectorID);
        return $query->getResult();

    }
}
