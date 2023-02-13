<?php

namespace App\Repository;

use App\Entity\PurchaseOrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PurchaseOrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseOrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseOrderProduct[]    findAll()
 * @method PurchaseOrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseOrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseOrderProduct::class);
    }

    // public function getAll()
    // {
    //     $entityManager = $this->getEntityManager();

    //     $query = $entityManager->createQuery(
    //         "SELECT pop
    //             FROM App\Entity\PurchaseOrderProduct pop
    //             ORDER BY pop.id ASC"
    //     );
    //     return $query->execute();
    // }

    // /**
    //  * @return PurchaseOrderProduct[] Returns an array of PurchaseOrderProduct objects
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
    public function findOneBySomeField($value): ?PurchaseOrderProduct
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
