<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Category::class);
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
            'SELECT c
        FROM App\Entity\Category c
        ORDER BY c.label ASC'
        );
        return $query->execute();
    }

    private function getSearchQuery(SearchData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('c');


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('c.label LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        $query
            ->addOrderBy("c.label", "ASC");
        return $query;
    }

    // /**
    //  * @return Category[] Returns an array of Categorie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Categorie
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
