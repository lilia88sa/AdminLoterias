<?php

namespace App\Repository;

use App\Entity\Post;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

     /**
      * @return Post[] Returns an array of Post objects
      */

    public function searchHomepagePost($value, $pageRequest)
    {
        $qb = $this->createQueryBuilder('p')
            ->orWhere('p.title LIKE :title')
            ->orWhere('p.description LIKE :description')
            ->andWhere('p.publish = :publicado')
            ->setParameter('title', '%' . $value . '%')
            ->setParameter('description', '%' . $value . '%')
            ->setParameter('publicado', true)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
        ;
        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $pageRequest->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $pagination;
    }

    public function findByViews(){
        $qb = $this->createQueryBuilder('p')
            ->select('SUM(p.views)')
            ->where('p.views IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
        return $qb;
    }

    public function findLatestNews(){

        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'category')
            ->innerJoin('category.orderClasification', 'orderclasification')
            ->andWhere('orderclasification.name LIKE :noticia')
            ->andWhere('p.publish = :publicado')

            ->setParameter('noticia', '%noticia%')
            ->setParameter('publicado', true)

            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(8)

            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function getTranslatedPostAtMonthCurrentDay($started_day ,$current_day){
        $queryBuilder = $this->createQueryBuilder('p');
        $qb = $queryBuilder
            ->select('COUNT(p.id) as ammount')
            ->addSelect('p.lastUserTranslation as user')
            ->addSelect('p.translationUpdate as date')
            ->andWhere('p.translationUpdate IS NOT NULL')
            ->andWhere('p.lastUserTranslation IS NOT NULL')
        ->andWhere('p.translationUpdate BETWEEN :filter_date_start and :filter_date_end')
            ->setParameter('filter_date_start', $started_day)
            ->setParameter('filter_date_end', $current_day)
           // ->orderBy('date')
               ->groupBy('user')
            ->getQuery()
            ->getArrayResult();
        return $qb;
    }


    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
