<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Review;
use App\Entity\Picture;
use App\Entity\Product;
use App\Data\SearchData;
use App\Form\ReviewType;
use App\Form\SearchType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/liste_produit", name="product_list")
     */
    public function productList(ProductRepository $repository, Request $request)
    {

        // On initialise les données
        $data = new SearchData();
        //on definit page et on recupère dans la requete la valeur qui correspond à page et si non defini elle est égal à 1
        $data->page = $request->get('page', 1);
        // On créé un formulaire qui utilise la class SearchType
        $form = $this->createForm(SearchType::class, $data);
        // Une fois le fomulaire construit on gère la requête qu'on passe en premier paramètre
        $form->handleRequest($request);

        //On recupère les valeurs min et max et on envoie en paramètre la recherche 
        [$min, $max] = $repository->findMinMax($data);
        //On recupère l'ensemble des produits lié à une recherche  et on lui envoie les données
        // $products = $repository->getBetterNote();
        $products  = $repository->findSearch($data);
        // Si on est dans le carde d'un requête ajax on return une reponse en json
        if ($request->get('ajax')) {
            // sleep(1); allonge le temps de reponse 
            return new JsonResponse([
                'content' => $this->renderView('product/_products.html.twig', ['products' => $products]),
                'sorting' => $this->renderView('product/_sorting.html.twig', ['products' => $products]),
                'pagination' => $this->renderView('product/_pagination.html.twig', ['products' => $products]),
                'pages' => ceil($products->getTotalItemCount() / $products->getItemNumberPerPage()),
                'min' => $min,
                'max' => $max
            ]);
        }
        return $this->render('product/list.html.twig', [
            'products' => $products,
            // 'review'=> $review,
            'form' => $form->createView(),
            'min' => $min,
            'max' => $max
        ]);
    }



    /**
     *
     * @Route("/search/bar", name="ajax_search")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {


        $requestString = $request->get('q');

        $entities = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findEntitiesByString($requestString);



        if (!$entities) {
            $result['entities']['error'] = "Aucun produit trouvé";
        } else {
            $result['entities'] =    $this->getRealEntities($entities);
        }

        $response = new JsonResponse(array_unique($result));

        return  $response;
    }

    public function getRealEntities($entities)
    {

        foreach ($entities as $entity) {
            $realEntities[$entity->getId()] = $entity->getLabel();
        }

        return array_unique($realEntities);
    }


    /**
     * @Route("/Produit/{id}", name="product_show")
     * 
     */

    public function showProduct(Product $product, Request $request): Response
    {

        if (!$product) {
            throw $this->createNotFoundException("Le produit recherché n'existe pas");
        }

        //On instancie l'entité Commentaires
        $reviews = new Review();
        $user = $this->getUser();

        //On crée l'objet form
        $form = $this->createForm(ReviewType::class, $reviews);


        //On récupère les données saisies
        $form->handleRequest($request);

        //On verifie si le formulaire a été envoyé et si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            //Le formulaire a été envoyé et les données sont valides

            $reviews->setProduct($product);
            $reviews->setUser($user);

            $reviews->setCreatedAt(new \DateTime('now'));

            //On instancie Doctrine
            $doctrine = $this->getDoctrine()->getManager();

            //On hydrate $reviews
            $doctrine->persist($reviews);

            //On écrit dans la bdd
            $doctrine->flush();
            $this->addFlash('success', "Votre commentaire a bien été pris en compte !");
        }
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'formReviews' => $form->createView()
        ]);

        return $this->redirectToRoute('product_show');
    }


    /**
     * @Route("/Produit/commentaire/{id}/delete" , name="review_remove" )
     * 
     */

    public function deleteReview(Review $review = null,UserRepository $user, ManagerRegistry $managerRegistry)
    {

        $id = $review->getProduct()->getId();
        $user = $this->getUser();

        if($user === $review->getUser()){
        $Entitymanager = $managerRegistry->getManager();
        $Entitymanager->remove($review);
        $Entitymanager->flush();
        $this->addFlash( 'success', "Commentaire supprimé" );
        } else {
            $this->addFlash( 'danger', "Vous ne pouvez supprimer que vos commentaires !!!!" );
        }


        return $this->redirect($this->generateUrl('product_show', array('id' => $id )));
    }





    // public function getProductWithNote
}


												
																					        

