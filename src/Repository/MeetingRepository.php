<?php

namespace App\Repository;

use App\Entity\Meeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Meeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meeting[]    findAll()
 * @method Meeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meeting::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Meeting $entity, bool $flush = true): void
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
    public function remove(Meeting $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    private function getQueryBuilder(QueryBuilder $qb = null) :QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('m');
    }

     /**
      * @return Meeting[] Returns an array of Meeting objects
      */


    public function findByIsUserOnAnotherMeeting($startTime, $endTime, $userID)
    {

       /* $sql = "SELECT m.id FROM meeting m
                              INNER JOIN user_in_meeting_meeting uimm ON m.id = uimm.meeting_id
                              INNER JOIN user_in_meeting uim ON uim.id = uimm.user_in_meeting_id
                              INNER JOIN user_in_meeting_user uimu ON uim.id = uimu.user_in_meeting_id
                              WHERE ((m.start BETWEEN :val1 AND :val2) OR (m.end BETWEEN :val1 AND :val2)
                              OR (:val1 BETWEEN m.start AND m.end) OR (:val2 BETWEEN m.start AND m.end))
                              AND (uim.is_going = 1 AND uimu.user_id = :id)";*/

        $sql = "SELECT m.id FROM App\Entity\Meeting m 
                              INNER JOIN App\Entity\UserInMeeting uim 
                              WHERE ((m.start BETWEEN :val1 AND :val2) OR (m.end BETWEEN :val1 AND :val2)
                              OR (:val1 BETWEEN m.start AND m.end) OR (:val2 BETWEEN m.start AND m.end))
                              AND (uim.is_going = 1 AND uim.user = :id)"; //???????????

        $em = $this->getEntityManager();
        $query = $em->createQuery($sql)
                    ->setParameter('id', $userID)
                    ->setParameter('val1', $startTime)
                    ->setParameter('val2', $endTime);

        return $query->getOneOrNullResult();

            /*$this->getQueryBuilder()
            ->andWhere('m.start BETWEEN :val1 AND :val2')
            ->orWhere('m.end BETWEEN :val1 AND :val2')
            ->orWhere(':val1 BETWEEN m.start AND m.end')
            ->orWhere(':val2 BETWEEN m.start AND m.end')
            ->setParameter('val1', $startTime)
            ->setParameter('val2', $endTime)
            ->getQuery()
            ->getResult()*/
    }


    /*
    public function findOneBySomeField($value): ?Meeting
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
