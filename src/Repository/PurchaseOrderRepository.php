<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\PurchaseOrder;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method PurchaseOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseOrder[]    findAll()
 * @method PurchaseOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseOrderRepository extends ServiceEntityRepository
{
    
    
    public function __construct(ManagerRegistry $registry , PaginatorInterface $paginator)
    {
        parent::__construct($registry, PurchaseOrder::class);
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
            'SELECT po
        FROM App\Entity\PurchaseOrder po
        ORDER BY po.id DESC'
        );
        return $query->execute();
    }

    private function getSearchQuery(searchData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('po');


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('po.id LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        $query
        ->addOrderBy("po.id", "ASC");
        return $query;
    }
}

    // /**
    //  * @return PurchaseOrder[] Returns an array of PurchaseOrder objects
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
    public function findOneBySomeField($value): ?PurchaseOrder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

