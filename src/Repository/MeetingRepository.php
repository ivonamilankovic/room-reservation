<?php

namespace App\Repository;

use App\Entity\Meeting;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\UserInMeeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
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
      * @return Meeting[]
      */
    public function findByIsUserOnAnotherMeeting($startTime, $endTime, $userID, $meetingID)
    {
         return $this->getQueryBuilder()
            ->leftJoin(UserInMeeting::class,'uim', Join::WITH, 'uim.meeting = m.id')
            ->addSelect('uim')
            ->andWhere('(m.start BETWEEN :val1 AND :val2) OR (m.end BETWEEN :val1 AND :val2) OR 
            (:val1 BETWEEN m.start AND m.end) OR (:val2 BETWEEN m.start AND m.end)')
            ->andWhere('uim.isGoing = 1 AND uim.declined = 0 AND m.id != :meetingID')
            ->andWhere('uim.user = :userID')
            ->setParameter('val1', $startTime)
            ->setParameter('val2', $endTime)
            ->setParameter('userID', $userID)
             ->setParameter('meetingID', $meetingID)
            ->getQuery()
            ->getResult()
           ;
    }

    /**
     * @return Meeting[]
     */
    public function findByIsRoomTakenForAnotherMeeting($startTime, $endTime, $roomID)
    {
        return  $this->getQueryBuilder()
            ->andWhere('(m.start BETWEEN :val1 AND :val2) OR (m.end BETWEEN :val1 AND :val2) OR 
            (:val1 BETWEEN m.start AND m.end) OR (:val2 BETWEEN m.start AND m.end)')
            ->andWhere('m.room = :roomID')
            ->setParameter('val1', $startTime)
            ->setParameter('val2', $endTime)
            ->setParameter('roomID', $roomID)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Meeting[]
     */
    public function findMyCreatedMeetings($userID)
    {
        return $this->getQueryBuilder()
            ->leftJoin(Room::class, 'r', Join::WITH, 'm.room = r.id')
            ->andWhere('m.creator = :val')
            ->andWhere('m.start > CURRENT_DATE()')
            ->setParameter('val', $userID)
            ->orderBy('m.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Meeting[]
     */
    public function findMeetingsByDateAndRoom($roomID, $date)
    {
        return $this->getQueryBuilder()
            ->andWhere('m.start LIKE \''.$date.' %\'')
            ->andWhere('m.room = :roomID ')
            ->setParameter(':roomID', $roomID)
            ->orderBy('m.start', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    /**
     * @return Meeting[]
     */
    public function findMeetingsByDate($date)
    {
        return $this->getQueryBuilder()
            ->andWhere('m.start LIKE \''.$date.' %\'')
            ->orderBy('m.start', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
