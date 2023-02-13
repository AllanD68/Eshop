<?php

namespace App\Repository;

use App\Entity\Genre;
use App\Data\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Genre::class);
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
            15
        );
    }

    public function getAll()

    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT g
        FROM App\Entity\Genre g
        ORDER BY g.label ASC'
        );
        return $query->execute();
    }

    private function getSearchQuery(searchData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('g');


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('g.label LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        $query
            ->addOrderBy("g.label", "ASC");
        return $query;
    }
}



    // /**
    //  * @return Genre[] Returns an array of Genre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Genre
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
