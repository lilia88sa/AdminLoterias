<?php

namespace App\Repository;

use App\Entity\Result;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Result|null find($id, $lockMode = null, $lockVersion = null)
 * @method Result|null findOneBy(array $criteria, array $orderBy = null)
 * @method Result[]    findAll()
 * @method Result[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }
    public function juegoClasification($id_juego,$fecha){return $this->getEntityManager()->createQuery(" SELECT count(r) FROM App\Entity\Result r where r.fecha='".$fecha."' and r.juego=".$id_juego)->getSingleScalarResult();}
    public function lastResultNacPosYear(){
        return $this->getEntityManager()->createQuery('SELECT r.pos_year FROM  App\Entity\Result r WHERE r.juego=2 ORDER BY r.fecha desc')->setMaxResults(1)->getSingleScalarResult();
    }

//    public function juegoClasification($id_juego,$fecha){
//
//       return $this->createQueryBuilder('r')
//           ->select('COUNT(r)')
//           ->andWhere('r.fecha = '.$fecha.' and r.juego = '.$id_juego)
//           ->getQuery()
//           ->getSingleScalarResult();
//
//    }



    // /**
    //  * @return Result[] Returns an array of Result objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Result
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
