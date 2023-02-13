<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(ProductRepository $product)
    {
    

          $productsNote = $this->getDoctrine()
            ->getRepository(Product::class)
            ->getBetterNoteHome();

            $date = new \DateTime('now');
            $productsDates = $this->getDoctrine()
            ->getRepository(Product::class)
            ->getProductByDateHome($date);

        return $this->render('accueil/index.html.twig', [
            'products' => $productsNote,
            'productsDates' => $productsDates
        ]);
    }
}