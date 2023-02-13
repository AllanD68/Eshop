<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Data\SearchData;
use App\Entity\Category;
use App\Entity\Platform;
use App\Form\SearchType;
use App\Entity\Conceptor;
use App\Form\CategoryType;
use App\Form\PlatformType;
use App\Form\ConceptorType;
use App\Repository\GenreRepository;
use App\Repository\CategoryRepository;
use App\Repository\PlatformRepository;
use App\Repository\ConceptorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")  
 */

class FilterController extends AbstractController
{
    /**
     * @Route("/filter", name="filter")
     */
    public function index()
    {
        return $this->render('filter/index.html.twig', [
            'controller_name' => 'FilterController',
        ]);
    }

    /**
     * @Route("/liste_genre", name="genre_list")
     * 
     */

    public function genreList(GenreRepository $repository, Request $request)
    {

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $genres = $repository->findSearch($data);

        return $this->render('admin/product/filter/genre/list_genre.html.twig', [
            'genres' => $genres,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/genre/ajout", name="genre_add")
     * @Route("/genre/{id}/edit", name="genre_edit")
     */

    public function add_editGenre(Genre $genre = null, Request $request)
    {
        // si le produit n'existe pas, on instancie un nouveau genre (on est dans le cas d'un ajout)
        if (!$genre) {
            $genre = new Genre();
        }



        // il faut créer un GenreType au préalable (php bin/console make:form)
        $form = $this->createForm(GenreType::class, $genre);

        // On récupere les donnés du formulaires
        $form->handleRequest($request);

        // si on soumet le formulaire et que le form est valide+
        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les données du formulaire
            $genre = $form->getData();

            // on ajoute le nouveau produit
            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde l'objet avant de l'envoyer en base de données
            $entityManager->persist($genre);

            // Execute la requête d'ajout/modification en base de donnés
            $entityManager->flush();

            // on redirige vers la liste des produits 
            //(admin_Genre_list étant le nom de la route qui liste tous les produits dans GenreController)
            return $this->redirectToRoute('genre_list');
        }

        /* on renvoie à la vue correspondante : 
           - le formulaire
           - le booléen editMode (si vrai, on est dans le cas d'une édition sinon on est dans le cas d'un ajout)
       */
        return $this->render('admin/product/filter/genre/add_edit.html.twig', [
            'formGenre' => $form->createView(),
            'genre' => $genre,
            'editMode' => $genre->getId() !== null
        ]);
    }


    /**
     * @Route("/Genre/{id}/delete" , name="genre_remove" )
     */

    public function deleteGenre(Genre $genre = null, ManagerRegistry $managerRegistry)
    {

        $Entitymanager = $managerRegistry->getManager();
        $Entitymanager->remove($genre);
        $Entitymanager->flush();

        $this->addFlash('success', 'Genre supprimé avec succes');

        return $this->redirectToRoute('genre_list');
    }

    // CATEGORY
    // ---------------------------------------------------------------

    /**
     * @Route("/liste_category", name="category_list")
     * 
     */

    public function categoryList(CategoryRepository $repository, Request $request)
    {

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $categories = $repository->findSearch($data);

        return $this->render('admin/product/filter/category/list_category.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/ajout", name="category_add")
     * @Route("/category/{id}/edit", name="category_edit")
     */

    public function add_editCategory(Category $category = null, Request $request)
    {
        // si le produit n'existe pas, on instancie un nouveau genre (on est dans le cas d'un ajout)
        if (!$category) {
            $category = new Category();
        }


        // il faut créer un GenreType au préalable (php bin/console make:form)
        $form = $this->createForm(CategoryType::class, $category);

        // On récupere les donnés du formulaires
        $form->handleRequest($request);

        // si on soumet le formulaire et que le form est valide+
        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les données du formulaire
            $category = $form->getData();

            // on ajoute le nouveau produit
            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde l'objet avant de l'envoyer en base de données
            $entityManager->persist($category);

            // Execute la requête d'ajout/modification en base de donnés
            $entityManager->flush();

            // on redirige vers la liste des produits 
            //(admin_Genre_list étant le nom de la route qui liste tous les produits dans GenreController)
        

            return $this->redirectToRoute('category_list');
        }

        /* on renvoie à la vue correspondante : 
           - le formulaire
           - le booléen editMode (si vrai, on est dans le cas d'une édition sinon on est dans le cas d'un ajout)
       */
        return $this->render('admin/product/filter/category/add_edit.html.twig', [
            'formCategory' => $form->createView(),
            'category' => $category,
            'editMode' => $category->getId() !== null
        ]);
    }


    /**
     * @Route("/category/{id}/delete" , name="category_remove" )
     */

    public function deleteCategory(Category $category = null, ManagerRegistry $managerRegistry)
    {

        $Entitymanager = $managerRegistry->getManager();
        $Entitymanager->remove($category);
        $Entitymanager->flush();

        $this->addFlash('success', 'Category supprimé avec succes');

        return $this->redirectToRoute('category_list');
    }


    // -------------------------------------------Conceptor---------------------------

 /**
     * @Route("/liste_concepteur", name="conceptor_list")
     * 
     */

    public function conceptorList(ConceptorRepository $repository, Request $request)
    {

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $conceptors = $repository->findSearch($data);

        return $this->render('admin/product/filter/conceptor/list_conceptor.html.twig', [
            'conceptors' => $conceptors,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/concepteur/ajout", name="conceptor_add")
     * @Route("/concepteur/{id}/edit", name="conceptor_edit")
     * @IsGranted("ROLE_ADMIN")  
     */

    public function add_editConceptor(Conceptor $conceptor = null, Request $request)
    {
        // si le produit n'existe pas, on instancie un nouveau genre (on est dans le cas d'un ajout)
        if (!$conceptor) {
            $conceptor = new Conceptor();
        }



        // il faut créer un GenreType au préalable (php bin/console make:form)
        $form = $this->createForm(ConceptorType::class, $conceptor);

        // On récupere les donnés du formulaires
        $form->handleRequest($request);

        // si on soumet le formulaire et que le form est valide+
        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les données du formulaire
            $conceptor = $form->getData();

            // on ajoute le nouveau produit
            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde l'objet avant de l'envoyer en base de données
            $entityManager->persist($conceptor);

            // Execute la requête d'ajout/modification en base de donnés
            $entityManager->flush();

            // on redirige vers la liste des produits 
            //(admin_Genre_list étant le nom de la route qui liste tous les produits dans GenreController)


            return $this->redirectToRoute('conceptor_list');
        }

        /* on renvoie à la vue correspondante : 
           - le formulaire
           - le booléen editMode (si vrai, on est dans le cas d'une édition sinon on est dans le cas d'un ajout)
       */
        return $this->render('admin/product/filter/conceptor/add_edit.html.twig', [
            'formConceptor' => $form->createView(),
            'conceptor' => $conceptor,
            'editMode' => $conceptor->getId() !== null
        ]);
    }


   
    /**
     * @Route("concepteur/{id}/delete" , name="conceptor_remove" )
     */

    public function deleteConceptor(Conceptor $conceptor = null, ManagerRegistry $managerRegistry)
    {

        $Entitymanager = $managerRegistry->getManager();
        $Entitymanager->remove($conceptor);
        $Entitymanager->flush();

        $this->addFlash('success', 'Concepteur supprimé avec succes');

        return $this->redirectToRoute('conceptor_list');
    }




    // ---------------------------------PLATFORM----------------------------------


     /**
     * @Route("/liste_Plateforme", name="platform_list")
     * 
     */

    public function PlatformList(PlatformRepository $repository, Request $request)
    {

        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $platforms = $repository->findSearch($data);

        return $this->render('admin/product/filter/platform/list_platform.html.twig', [
            'platforms' => $platforms,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/plateform/ajout", name="platform_add")
     * @Route("/plateform/{id}/edit", name="platform_edit")
     */

    public function PlatformConceptor(Platform $platform = null, Request $request)
    {
        // si le produit n'existe pas, on instancie un nouveau genre (on est dans le cas d'un ajout)
        if (!$platform) {
            $platform = new Platform();
        }



        // il faut créer un GenreType au préalable (php bin/console make:form)
        $form = $this->createForm(PlatformType::class, $platform);

        // On récupere les donnés du formulaires
        $form->handleRequest($request);

        // si on soumet le formulaire et que le form est valide+
        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les données du formulaire
            $platform = $form->getData();

            // on ajoute le nouveau produit
            $entityManager = $this->getDoctrine()->getManager();

            // Sauvegarde l'objet avant de l'envoyer en base de données
            $entityManager->persist($platform);

            // Execute la requête d'ajout/modification en base de donnés
            $entityManager->flush();

            // on redirige vers la liste des produits 
            //(admin_Genre_list étant le nom de la route qui liste tous les produits dans GenreController)


            return $this->redirectToRoute('platform_list');
        }

        /* on renvoie à la vue correspondante : 
           - le formulaire
           - le booléen editMode (si vrai, on est dans le cas d'une édition sinon on est dans le cas d'un ajout)
       */
        return $this->render('admin/product/filter/platform/add_edit.html.twig', [
            'formPlatform' => $form->createView(),
            'platform' => $platform,
            'editMode' => $platform->getId() !== null
        ]);
    }


     /**
     * @Route("/plateforme/{id}/delete" , name="platform_remove" )
     */

    public function deletePlatform(Platform $plateform = null, ManagerRegistry $managerRegistry)
    {

        $Entitymanager = $managerRegistry->getManager();
        $Entitymanager->remove($plateform);
        $Entitymanager->flush();

        $this->addFlash('success', 'Plateforme supprimé avec succes');

        return $this->redirectToRoute('platform_list');
    }




}
