<?php

namespace App\Repository;

use App\Entity\Meeting;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\UserInMeeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserInMeeting>
 *
 * @method UserInMeeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInMeeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInMeeting[]    findAll()
 * @method UserInMeeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInMeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInMeeting::class);
    }

    public function add(UserInMeeting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserInMeeting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function getQueryBuilder(QueryBuilder $qb = null) :QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('uim');
    }

    /**
     * @return UserInMeeting[]
     */
    public function findAllFutureMeetings($userID)
    {
        return $this->getQueryBuilder()
            ->leftJoin(Meeting::class, 'm', Join::WITH, 'm.id = uim.meeting')
            ->leftJoin(Room::class, 'r', Join::WITH, 'm.room = r.id')
            //->addSelect(['m', 'r']) //ne treba jer pravi problem za pristup u twigu jer ne znam zasto
            ->andWhere(' uim.user = :val AND uim.isGoing = 1')
            ->andWhere('m.start > CURRENT_DATE()')
            ->setParameter('val', $userID)
            ->orderBy('m.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return UserInMeeting[]
     */
    public function findRequestsForMeetings($userID)
    {
        return $this->getQueryBuilder()
            ->leftJoin(Meeting::class, 'm', Join::WITH, 'm.id = uim.meeting')
            ->leftJoin(Room::class, 'r', Join::WITH, 'm.room = r.id')
            ->andWhere(' uim.user = :val AND uim.isGoing = 0 AND uim.declined = 0')
            ->setParameter('val', $userID)
            ->orderBy('m.start', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

}
