<?php

namespace App\Repository;

use App\Entity\Juego;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Juego|null find($id, $lockMode = null, $lockVersion = null)
 * @method Juego|null findOneBy(array $criteria, array $orderBy = null)
 * @method Juego[]    findAll()
 * @method Juego[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JuegoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Juego::class);
    }
    public function juegoClas(){return $this->getEntityManager()->createQuery("SELECT j as juego,oc.name as clasificacion FROM App\Entity\Juego j join j.orderClasification oc")->getResult();}

    //public function lastResultXgame(){return $this->getEntityManager()->createQuery(" SELECT r FROM App\Entity\Result r GROUP BY r.juego ORDER BY r.juego asc,r.fecha asc")->getResult();}
	
    public function lastResultNac(){
		return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=2 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
	}
	public function lastResultEurom(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=5 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
    }
    public function lastResultPrimitiva(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=9 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
    }
    public function lastResultBonoloto(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=6 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
    }
    public function lastResultGordo(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=1 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
    }
    public function lastResultLototurf(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=7 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
    }
    public function lastResultQuintuple(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=8 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
    }
    public function lastResultQuiniela(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=4 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
    }
    public function lastResultQuinigol(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=3 ORDER BY r.fecha desc')->setMaxResults(1)->getResult()[0];
    }

    public function last10ResultQuinigol(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=3 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }
    public function last10ResultQuiniela(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=4 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }
    public function last10ResultQuintuple(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=8 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }
    public function last10ResultLototurf(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=7 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }
    public function last10ResultNac(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=2 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }
    public function last10ResultEurom(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=5 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }
    public function last10ResultPrimitiva(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=9 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }
    public function last10ResultBonoloto(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=6 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }
    public function last10ResultGordo(){
        return $this->getEntityManager()->createQuery('SELECT r FROM  App\Entity\Result r WHERE r.juego=1 ORDER BY r.fecha desc')->setMaxResults(10)->getResult();
    }



	public function lastResultNacPosYear(){
		return $this->getEntityManager()->createQuery('SELECT r.pos_year FROM  App\Entity\Result r WHERE r.juego=2 ORDER BY r.fecha desc')->setMaxResults(1)->getSingleScalarResult();
	}
    public function lastResultXgame(){
        $qs=[];
        foreach (range(1,9) as $i)
            $qs=array_merge($qs,$this->getEntityManager()->createQuery("SELECT r FROM  App\Entity\Result r WHERE r.juego=$i ORDER BY r.fecha desc")->setMaxResults(1)->getResult());

        return $qs;//$this->getEntityManager()->createQuery($q)->getResult();

    }

    public function last5Results(){
        $qs=[];
        foreach (range(1,9) as $i)
            $qs[]=$this->getEntityManager()->createQuery("SELECT r FROM  App\Entity\Result r join r.juego j WHERE j.order_home=$i ORDER BY r.fecha desc")->setMaxResults(5)->getResult();

        return $qs;
    }
	
	 public function lastResultsXgame(){
        $qs=[];
        foreach (range(1,9) as $i)
            $qs[$i]=$this->getEntityManager()->createQuery("SELECT r FROM  App\Entity\Result r WHERE r.juego=$i ORDER BY r.fecha desc")->getResult();
        // dump($qs);die;
        return $qs;//$this->getEntityManager()->createQuery($q)->getResult();

    }


//    public function lastResultXgame(){return $this->getEntityManager()->createQuery("SELECT r FROM App\Entity\Result r WHERE r.fecha in (SELECT MAX(d.fecha) FROM App\Entity\Result d  GROUP BY d.juego)GROUP BY r.juego")->getResult();}

    public function juegoResultFechasDesc($id){return $this->getEntityManager()->createQuery("SELECT t  FROM App\Entity\Result t join t.juego r where r.id=$id order by t.fecha desc")->setMaxResults(12)->getResult();}

//    public function juegoClas(){
//
//        return $this->createQueryBuilder('g')
//            ->select('g as juego,oc.name as clasificacion')
//            ->innerJoin('g.orderClasification','oc')
//            ->getQuery()
//            ->getResult();
//
//    }


    // /**
    //  * @return Juego[] Returns an array of Juego objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Juego
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
