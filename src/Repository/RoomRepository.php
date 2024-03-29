<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Room $entity, bool $flush = true): void
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
    public function remove(Room $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    private function getQueryBuilder(QueryBuilder $qb = null) :QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('r');
    }

     /**
      * @return Room[]
      */

    public function findByCity($value)
    {
        return $this->getQueryBuilder()
            ->andWhere('r.city = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Room[]
     */
    public function findAllCities()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT r.city FROM App\Entity\Room r GROUP BY r.city')
            ->getResult();
    }

}
