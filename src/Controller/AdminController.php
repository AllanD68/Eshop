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
use App\Entity\PurchaseOrderProduct;
use App\Repository\ReviewRepository;
use App\Repository\ProductRepository;
use App\Repository\PurchaseOrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Repository\PurchaseOrderProductRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")  
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/panneau_admin", name="admin_pannel") 
     */
    public function adminPannel()
    {
        return $this->render('admin/admin_pannel.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin_liste_utilisateur", name="admin_user_list")
     */
    public function getUsers(UserRepository $repository, Request $request)
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $users = $repository->findSearch($data);

        return $this->render('admin/user/list.html.twig', [
            'users' => $users,
            'form' => $form->createView()
        ]);
    }





    /**
     * @Route("/User/{id}/delete" , name="user_remove" )
     * 
     */

    public function deleteUserAdmin(User $user = null, ManagerRegistry $managerRegistry)
    {

        $redirectId = $user->getId();
        $Entitymanager = $managerRegistry->getManager();

        $em = $this->getDoctrine()->getManager();

        $reviewRepository = $em->getRepository(Review::class);
        if ($user->getEmail() != 'profil supprimé') {
            $reviews = $reviewRepository->findBy(['user' => $this->getUser()]);
            foreach ($reviews as $review) {

                $review->setUser(null);
                // dd($reviews);
            }

            $user->setEmail('profil supprimé');
            $user->setRoles(null);
            $user->setRoles('profil supprimé');
            $em->persist($user);
            $em->flush();
            $Entitymanager->flush();
            $this->addFlash('success', 'Le compte de l\'utilisateur a bien été supprimé !');

            // return $this->redirect($this->generateUrl('user_show', array('id' => $redirectId)));
        } else
            $this->addFlash('warning', 'Le compte de l\'utilisateur est déja supprimé !');

            return $this->redirect($this->generateUrl('user_show', array('id' => $redirectId)));
    }


    /**
     * @Route("/User/{id}", name="user_show", methods="GET")
     * 
     */
    public function showUserAdmin(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user
        ]);
    }



    /**
     * @Route("/admin_liste_produit", name="admin_product_list")
     * 
     */

    public function adminProductList(ProductRepository $repository, Request $request)
    {

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $products = $repository->findSearch($data);

        return $this->render('admin/product/admin_list.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/ajout" , name="product_add"))  
     */
    public function addProductAdmin(Product $product = null, Request $request)
    {
        // si le produit n'existe pas, on instancie un nouveau Product (on est dans le cas d'un ajout)
        if (!$product) {
            $product = new Product();
        }



        // il faut créer un ProductType au préalable (php bin/console make:form)
        $form = $this->createForm(ProductType::class, $product);

        // On récupere les donnés du formulaires
        $form->handleRequest($request);

        // si on soumet le formulaire et que le form est valide+
        if ($form->isSubmitted() && $form->isValid()) {

            //On recupère les images transmises
            $pictures = $form->get('pictures')->getData();

            //On boucle sur les images
            foreach ($pictures as $picture) {
                // On génère un nouveau nom de fichier 
                $files = md5(uniqid()) . '.' . $picture->guessExtension();

                //On copies le fichier dans le dossier img
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $files
                );

                //On stock l'image dans la base de données (le lien)
                $pic = new Picture();
                $pic->setLink($files);
                $product->addPicture($pic);
            }

            // on récupère les données du formulaire
            $product = $form->getData();

            // on ajoute le nouveau produit
            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde l'objet avant de l'envoyer en base de données
            $entityManager->persist($product);

            // Execute la requête d'ajout/modification en base de donnés
            $entityManager->flush();

            // on redirige vers la liste des produits 
            $id = $product->getId();
            // on redirige vers la liste des produits 

            return $this->redirect($this->generateUrl('product_edit', array('id' => $id)));
        }

        /* on renvoie à la vue correspondante : 
           - le formulaire
       */
        return $this->render('admin/product/add.html.twig', [
            'formProduct' => $form->createView(),
            'product' => $product,
            'productId' => $product->getId() !== null

        ]);
    }

    /**
     * @Route("/{id}/edition", name="product_edit")  
     */

    public function editProductAdmin(Product $product = null, Request $request)
    {



        // il faut créer un ProductType au préalable (php bin/console make:form)
        $form = $this->createForm(ProductType::class, $product);

        // On récupere les donnés du formulaires
        $form->handleRequest($request);

        // si on soumet le formulaire et que le form est valide+
        if ($form->isSubmitted() && $form->isValid()) {

            //On recupère les images transmises
            $pictures = $form->get('pictures')->getData();

            //On boucle sur les images
            foreach ($pictures as $picture) {
                // On génère un nouveau nom de fichier 
                $files = md5(uniqid()) . '.' . $picture->guessExtension();

                //On copies le fichier dans le dossier img
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $files
                );

                //On stock l'image dans la base de données (le lien)
                $pic = new Picture();
                $pic->setLink($files);
                $product->addPicture($pic);
            }

            // on récupère les données du formulaire
            $product = $form->getData();

            // on ajoute le nouveau produit
            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde l'objet avant de l'envoyer en base de données
            $entityManager->persist($product);

            // Execute la requête d'ajout/modification en base de donnés
            $entityManager->flush();


            $id = $product->getId();
            // on redirige vers la liste des produits 

            return $this->redirect($this->generateUrl('product_edit', array('id' => $id)));
        }

        /* on renvoie à la vue correspondante : 
           - le formulaire
           - le Mode Edition
       */
        return $this->render('admin/product/edit.html.twig', [
            'formProduct' => $form->createView(),
            'product' => $product,
            'productId' => $product->getId() !== null

        ]);
    }




    /**
     * @Route("/Product/{id}/delete" , name="product_remove" )
     * 
     */

    public function deleteProductAdmin(Product $product = null, ManagerRegistry $managerRegistry)
    {

        $Entitymanager = $managerRegistry->getManager();
        $Entitymanager->remove($product);
        $Entitymanager->flush();

        return $this->redirectToRoute('admin_product_list');
    }



    /**
     * @Route("/Product/{id}", name="admin_product_show", methods="GET")
     * 
     */
    public function showProductAdmin(Product $product): Response
    {
        return $this->render('admin/product/admin_product_show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/supprimer/image/{id}", name="delete_picture" , methods={"DELETE"})
     */
    public function deletePicture(Picture $picture, Request $request)
    {

        $data = json_decode($request->getContent(), true);

        //On verifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {
            //On récupère le nom de l'image
            $link = $picture->getLink();
            // On supprime le fichier
            unlink($this->getParameter('pictures_directory') . '/' . $link);

            //On supprimer l'entrée de la base 
            $em = $this->getDoctrine()->getManager();
            $em->remove($picture);
            $em->flush();

            //On répond en Json 
            return new JsonResponse(['succes' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }


    /**
     * @Route("/liste_commande", name="admin_purchase_list")
     * 
     */

    public function purchaseOrderList(PurchaseOrderRepository $repository, Request $request)
    {

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $purchaseOrders = $repository->findSearch($data);

        return $this->render('admin/purchaseOrder/purchase.html.twig', [
            'purchaseOrders' => $purchaseOrders,
            'form' => $form->createView()
        ]);
    }




    /**
     * @Route("/liste_commentaire", name="review_list")
     * 
     */


    public function reviewList(ReviewRepository $repository, Request $request)
    {

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $reviews = $repository->findSearch($data);

        return $this->render('admin/review.html.twig', [
            'reviews' => $reviews,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/commentaire/{id}/delete" , name="review_remove_admin" )
     * 
     */

    public function deleteReviewAdmin(Review $review = null, ManagerRegistry $managerRegistry)
    {
        $id = $review->getProduct()->getId();

        $Entitymanager = $managerRegistry->getManager();
        $Entitymanager->remove($review);
        $Entitymanager->flush();
        $this->addFlash('success', "Commentaire supprimé");


        return $this->redirect($this->generateUrl('admin_product_show', array('id' => $id)));
    }
}
