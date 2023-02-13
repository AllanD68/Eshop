<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Platform;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Platform|null find($id, $lockMode = null, $lockVersion = null)
 * @method Platform|null findOneBy(array $criteria, array $orderBy = null)
 * @method Platform[]    findAll()
 * @method Platform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlatformRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Platform::class);
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
            'SELECT pf
        FROM App\Entity\Platform pf
        ORDER BY pf.label ASC'
        );
        return $query->execute();
    }

    private function getSearchQuery(searchData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('pf');


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('pf.label LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        $query
            ->addOrderBy("pf.label", "ASC");
        return $query;
    }
    // /**
    //  * @return Platform[] Returns an array of Platform objects
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
    public function findOneBySomeField($value): ?Platform
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
