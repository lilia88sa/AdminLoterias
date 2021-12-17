<?php

namespace App\Repository;

use App\Entity\NumeroPremiado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NumeroPremiado|null find($id, $lockMode = null, $lockVersion = null)
 * @method NumeroPremiado|null findOneBy(array $criteria, array $orderBy = null)
 * @method NumeroPremiado[]    findAll()
 * @method NumeroPremiado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NumeroPremiadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NumeroPremiado::class);
    }
    public function allNumPrem(){
		
		$nums=array_map(function($v){return ['id'=>$v->getId(),'fecha'=>$v->getFecha(),'array'=>json_decode($v->getArrayJson())];},$this->findAll());
		
		return $nums;
		//return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=2 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
	}

    // /**
    //  * @return NumeroPremiado[] Returns an array of NumeroPremiado objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NumeroPremiado
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
