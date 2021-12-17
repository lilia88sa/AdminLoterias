<?php

namespace App\Repository;

use App\Entity\Entities;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Entities|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entities|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entities[]    findAll()
 * @method Entities[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntitiesRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Entities::class);
        $this->paginator = $paginator;
    }

    public function searchHomepageEntity($value, $pageRequest)
    {
        $qb = $this->createQueryBuilder('e')

            ->orWhere('e.comercialName LIKE :title')
            ->orWhere('e.name LIKE :title')
            ->orWhere('e.serviceDescription LIKE :title')
            ->orWhere('e.description LIKE :description')
            ->andWhere('e.publish = :publicado')
            ->setParameter('title', '%' . $value . '%')
            ->setParameter('description', '%' . $value . '%')
            ->setParameter('publicado', true)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
        ;
        $pagination = $this->paginator->paginate(
            $qb, /* query NOT result */
            $pageRequest->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $pagination;
    }
    // /**
    //  * @return Entities[] Returns an array of Entities objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Entities
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
