<?php

namespace App\Repository;

use App\Entity\KeyWords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method KeyWords|null find($id, $lockMode = null, $lockVersion = null)
 * @method KeyWords|null findOneBy(array $criteria, array $orderBy = null)
 * @method KeyWords[]    findAll()
 * @method KeyWords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeyWordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KeyWords::class);
    }

    // /**
    //  * @return KeyWords[] Returns an array of KeyWords objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?KeyWords
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
