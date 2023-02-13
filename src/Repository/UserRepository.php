<?php

namespace App\Repository;

use App\Entity\User;
use App\Data\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
        $this->paginator = $paginator;
    }


    public function getAll()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
        'SELECT u
        FROM App\Entity\User u
        ORDER BY u.inscriptionDate ASC'
        );
        return $query->execute();
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


    private function getSearchQuery(SearchData $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('u');


        if (!empty($search->q)) {
            $query = $query
                ->andWhere('u.email LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        $query
            ->addOrderBy("u.email", "ASC");
        return $query;
    }


    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
