<?php

namespace App\Repository;

use App\Entity\Review;
use App\Data\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Review::class);
        $this->paginator = $paginator;
    }


    /**
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
    {

        $query = $this->getSearchQuery($search)->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            20
        );
    }

    public function getAll()

    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT r
        FROM App\Entity\Review r
        ORDER BY r.created_at ASC'
        );
        return $query->execute();
    }

    private function getSearchQuery(searchData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('r');


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('r.product LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        $query
            ->addOrderBy("r.created_at", "ASC");
        return $query;
    }
}


        // public function findBestAds($limit){
        //     return $this->createQueryBuilder('a')
        //                 ->select('a as annonce, AVG(c.rating) as avgRatings')
        //                 ->join('a.comments', 'c')
        //                 ->groupBy('a')
        //                 ->orderBy('avgRatings', 'DESC')
        //                 ->setMaxResults($limit)
        //                 ->getQuery()
        //                 ->getResult();
        // }

    // /**
    //  * @return Review[] Returns an array of Review objects
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
    public function findOneBySomeField($value): ?Review
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
