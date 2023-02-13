<?php

namespace App\Repository;

use App\Entity\Product;
use App\Data\SearchData;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{   //On implemente la paginatiion (PaginatorInterface)
    public function __construct(ManagerRegistry $registry , PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }


    public function findEntitiesByString($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p
                FROM  App\Entity\Product p
                WHERE p.label LIKE :str'
            )
            ->setParameter('str', "%{$str}%")
            ->getResult();
    }

    



    /**
     * // On récupère les produits en lien avec une recherche
     * @return PaginationInterface
     */
    // On prend le paramètres de de type SearchData qui seront les données lié à la recherche 
    public function findSearch(SearchData $search): PaginationInterface
    {
    
        // $query = $this->getBetterNote();

        //On recupère notre requête , on passe en paramètre la recherche
        $query = $this->getSearchQuery($search)->getQuery();

        // On la passe au paginator
        return $this->paginator->paginate(
        // en premier parametre notre requête
        $query,
        // en second le numéro de page (dans searchData)
        $search->page,
        // en dernier le nombre d'item par page
        15
    );
  }

  public function getBetterNoteHome(){
    $entityManager = $this->getEntityManager();
    $query = $entityManager->createQuery(
        'SELECT  r , p
        FROM App\Entity\Product p
        INNER JOIN p.reviews r
        ORDER BY r.note DESC'
    );
    // $query->setParameter('product', $product);
    return $query->execute();
  }


//   public function getBetterNoteHome($product){
//     return $this->getEntityManager()
//     ->createQueryBuilder()
//     ->select('p')
//     ->from('App\Entity\Product', 'p')
//     ->join('App\Entity\Review','r')
//     ->setParameter('product', $product)
//     ->addOrderBy("r.note","DESC")
//     ->setMaxResults(10)
//     ->getQuery()
//     ->getResult();
// }

public function getProductByDateHome($date){
    return $this->getEntityManager()
    ->createQueryBuilder()
    ->select('p')
    ->from('App\Entity\Product', 'p')
    ->where('p.releaseDate <  :date')
    ->setParameter('date', $date)
    ->addOrderBy("p.releaseDate","DESC")
    ->setMaxResults(10)
    ->getQuery()
    ->getResult();
}

//   public function findAllCatalog()
//     {
//         $em = $this->getEntityManager();
//         $qb = $em->createQueryBuilder();
//         $qb->select('c')
//         ->from('RetailMappingCatalogBundle:SkuInventory', 'c')
//         ->Join('c.catalogId', 'cl')
//         ->orderBy('c.id', 'DESC');
//         //dump($qb->getQuery());die;
//         return $qb->getQuery();
//     }



  

  

  public function getAll()

  {
    $entityManager = $this->getEntityManager();
    $query = $entityManager->createQuery(
        'SELECT p
        FROM App\Entity\Product p
        ORDER BY p.label ASC'
    );
    return $query->execute();
}

  /**
   * Récupère le prix min et max correspondant à une recherhce
   * @return integer[] 
   */
  public function findMinMax(SearchData $search): array 
  {
        // true = on ignore les filtres concernant le prix
      $result = $this->getSearchQuery($search, true)
      // on selectionnes les valeurs min et max
      ->select('MIN(p.price) as min', 'MAX(p.price) as max')
      ->getQuery()
      // On renvoit en valeur minimal result à l'index 0 avec la clé min
      // On renvoit en valeur max result à l'index 0 avec la clé max
      ->getScalarResult();
    return [(int)$result[0]['min'], (int)$result[0]['max']];
  }

  private function getSearchQuery (searchData $search, $ignorePrice = false): QueryBuilder
  {
    $query = $this
    // On récupère les produits
    ->createQueryBuilder('p')
    //On selectionne toutes les info liées aux filtres et aux produits , ce qui permet de récupèrer toutes les info en une requête
    ->select('g' , 'pf' , 'c' ,'p' , 'cg')
    // On fait une liaison avec les différents filtres
    ->join('p.genres' ,  'g')
    ->join('p.platforms', 'pf')
    ->join('p.conceptor', 'c')
    ->join('p.category', 'cg');

  
 // si on a au moins une valeure a été rentré pour q
    if (!empty($search->q)) {
        $query = $query
        // On dit que le nom de notre produit soit comme le paramètre q
        ->andWhere('p.label LIKE :q')
        // permet de faire des recherches partielles 
        ->setParameter('q' , "%{$search->q}%");
    }


    // si on a au moins une valeure a été rentré pour min
    if(!empty($search->min && $ignorePrice === false)){
        $query = $query
         // on demande que la valeure soit supérieur ou égal à la valeur min
        ->andWhere('p.price >= :min')
        ->setParameter('min' , $search->min);
    }

    // si on a au moins une valeure a été rentré pour max
    if(!empty($search->max && $ignorePrice === false)){
        $query = $query
        // on demande que la valeure soit inférieur ou égal à la valeur max
        ->andWhere('p.price <= :max')
        // on passe un nouveau paramètre 
        ->setParameter('max' , $search->max);
    }

    // Si on une recherce pour new
    if(!empty($search->new)){
        $query = $query
        // On dit que new est égal à 1
        ->andWhere('p.new = 1');
    }


    // Si on une recherce pour Genre
    if (!empty($search->genres)) {
        $query = $query
        // On veut que l'id soit dans une liste qu'on lui enverra 
        ->andWhere('g.id IN (:genres)')
        // On lui envoie un paramètre genre qui sera notre liste de genre 
        ->setParameter('genres' , $search->genres );
    }

    if (!empty($search->platforms)) {
        $query = $query
        ->andWhere('pf.id IN (:platforms)')
        ->setParameter('platforms' , $search->platforms );
    }

    if (!empty($search->conceptor)) {
        $query = $query
        ->andWhere('c.id IN (:conceptor)')
        ->setParameter('conceptor' , $search->conceptor );
    }

    if (!empty($search->category)) {
        $query = $query
        ->andWhere('cg.id IN (:category)')
        ->setParameter('category' , $search->category );
    }
    $query 
    ->addOrderBy("p.label","ASC");
     return $query;
  }

}



    // /**
    //  * @return Query
    //  */

    // public function findAllVisibleQuery(): Query
    // {
    //     return $this->findAllVisibleQuery()
    //     ->getQuery();
    // }
    

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */