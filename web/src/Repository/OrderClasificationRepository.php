<?php

namespace App\Repository;

use App\Entity\OrderClasification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderClasification|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderClasification|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderClasification[]    findAll()
 * @method OrderClasification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderClasificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderClasification::class);
    }

    // /**
    //  * @return OrderClasification[] Returns an array of OrderClasification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderClasification
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
