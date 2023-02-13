<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Conceptor;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Conceptor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conceptor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conceptor[]    findAll()
 * @method Conceptor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConceptorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Conceptor::class);
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
            'SELECT cp
        FROM App\Entity\Conceptor cp
        ORDER BY cp.label ASC'
        );
        return $query->execute();
    }

    private function getSearchQuery(searchData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('cp');


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('cp.label LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        $query
            ->addOrderBy("cp.label", "ASC");
        return $query;
    }

    // /**
    //  * @return Conceptor[] Returns an array of Conceptor objects
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
    public function findOneBySomeField($value): ?Conceptor
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
