<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Product;
use App\Entity\PurchaseOrder;
use App\Form\PurchaseOrderType;
use App\Entity\PurchaseOrderProduct;
use App\Repository\PurchaseOrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


// Include Dompdf required namespaces
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseOrderController extends AbstractController
{
    /**
     * @Route("/commande/adresse_livraison", name="purchase_coordinate")
     *  @IsGranted("ROLE_USER")  
     */
    public function addCoordinate(PurchaseOrder $purchaseOrder = null, Request $request, \Swift_Mailer $mailer)
    {

        // il faut créer un GenreType au préalable (php bin/console make:form)
        $form = $this->createForm(PurchaseOrderType::class, $purchaseOrder);

        // On récupere les donnés du formulaires
        $form->handleRequest($request);

        // si on soumet le formulaire et que le form est valide
        if ($form->isSubmitted() && $form->isValid()) {
            //On créé un objet commmande
            $purchaseOrder = new PurchaseOrder();
            // on récupère les données du formulaire


            //On recupère la session
            $session = $request->getSession();

            // si le panier est rempli
            if (!($session->has('cart'))) {
                //$session->set('cart', array());

                $this->addFlash('danger', 'Votre panier est vide');
                return $this->redirectToRoute('cart_index');
            } else
                //On recupère le panier
                $cart = $session->get('cart');


            //On communique avec la bdd
            $em = $this->getDoctrine()->getManager();

            // $products = $em->getRepository(Product::class)->findBy(array_keys($cart));

            $products = array();
            foreach (array_keys($session->get('cart')) as $prod) {
                $products[] = $em->getRepository(Product::class)->find($prod);
                // dd($cart[$prod]);
                foreach ($products as $product) {
                    if (($cart[$prod]) > $product->getStock()) {
                        $this->addFlash('danger', 'Un produit est soit indisponible soit en quantité trop importante par rapport au stock');
                        return $this->redirectToRoute('cart_index');
                    }
                }
            }

            $total = 0;

            foreach ($products as $product) {
                $totalItem = ($product->getPrice() * $cart[$product->getId()]);
                $total += floatval($totalItem);

              

                // pop = PurchaseOrderProduct/Commande_Produit
                $pop = new PurchaseOrderProduct();
                $pop->setQty($cart[$product->getId()]);
                $product->setStock(($product->getStock()) - ($cart[$product->getId()]));
                $pop->setProducts($product);
                $pop->setPurchaseOrders($purchaseOrder);
                $em->persist($pop);
            }
            // dd($purchaseOrder->getPc());
            $data = $form->getData();
            // $PurchaseOrder->setCartCmd($cartCmd);
            $purchaseOrder->setTotal($total);
            $purchaseOrder->setUser($this->getUser());
            $purchaseOrder->setPc($data->getPc());
            $purchaseOrder->setCity($data->getCity());
            $purchaseOrder->setAdress($data->getAdress());
            $purchaseOrder->setCreatedAt(new \DateTime('now'));
            $em->persist($purchaseOrder);
            $em->flush();
            $session->remove('cart');

            $user = $this->getUser();

            $message = (new \Swift_Message('Commande GAMEIFY'))
                ->setFrom('votre@adresse.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        // templates/hello/email.txt.twig
                        'email/email_purchase.html.twig'

                    )
                );

            // On envoie l'e-mail
            $mailer->send($message);

            $this->addFlash('success', 'Commande validée avec succes , un mail va être envoyé');

            return $this->redirectToRoute('purchase_done');
        };



        return $this->render('purchase_order/purchase_coordinate.html.twig', [
            'formCoordinate' => $form->createView(),
            'purchaseOrder' => $purchaseOrder
        ]);
    }

    /**
     * @Route("/commande/valide", name="purchase_done" )
     *  @IsGranted("ROLE_USER")  
     */

    public function purchaseDone()
    {

        // $user = $this->getUser();

        // $purchaseOrder = $this->getDoctrine()->getRepository(PurchaseOrder::class);

        return $this->render('purchase_order/purchase_done.html.twig');
    }


    /**
     * @Route("/commande/{id}/pdf", name="purchase_pdf")
     *  @IsGranted("ROLE_USER")  
     */

    public function pdf(PurchaseOrder $purchaseOrder)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $user = $this->getUser();



        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('purchase_order/pdf_recap.html.twig', [
            'title' => "Facture",
            'purchaseOrder' => $purchaseOrder,
            'user' => $user,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();


        ob_end_clean();
        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("commande_gamify.pdf", [
            "Attachment" => true
        ]);
    }
    // /**
    //  * @Route("/commande/confirmation", name="purchase_create")
    //  * @IsGranted("ROLE_USER")  
    //  */
    // public function newPurchaseOrder(Request $request)
    // {
    //     //On recupère la session
    //     $session = $request->getSession();
    //     //On créé un objet commmande
    //     $purchaseOrder = new PurchaseOrder();
    //     // si le panier est rempli
    //     if (!($session->has('cart'))) {
    //         //$session->set('cart', array());
    //         return $this->redirectToRoute('cart_index');
    //     }
    //     //On recupère le panier
    //     $cart = $session->get('cart');

    //     //On communique avec la bdd
    //     $em = $this->getDoctrine()->getManager();

    //     // $products = $em->getRepository(Product::class)->findBy(array_keys($cart));

    //     $products = array();
    //     foreach (array_keys($session->get('cart')) as $prod) {
    //         $products[] = $em->getRepository(Product::class)->find($prod);
    //     }

    //     //$products = $em->getRepository(Article::class)->getArray(array_keys($cart));

    //     //On créé la commande vide
    //     $cartCmd = [];
    //     //On créé le total à 0
    //     $total = 0;

    //     foreach ($products as $product) {
    //         $totalItem = ($product->getPrice() * $cart[$product->getId()]);
    //         $total += floatval($totalItem);

    //         $cartCmd[$product->getId()] = [
    //             'label' => $product->getLabel(),
    //             'description' => $product->getDescription(),
    //             'price' => $product->getprice(),
    //             'qty' => intval($cart[$product->getId()]),
    //             'totalItem' => $totalItem,
    //         ];

    //         // pop = PurchaseOrderProduct/Commande_Produit
    //         $pop = new PurchaseOrderProduct();
    //         $pop->setQty($cart[$product->getId()]);
    //         $product->setStock(($product->getStock()) - ($cart[$product->getId()]));
    //         $pop->setProducts($product);
    //         $pop->setPurchaseOrders($purchaseOrder);
    //         $em->persist($pop);
    //     }
    //     // $PurchaseOrder->setCartCmd($cartCmd);
    //     $purchaseOrder->setTotal(100);
    //     $purchaseOrder->setUser($this->getUser());
    //     // $purchaseOrder->setAdress('test');
    //     // $purchaseOrder->setPc('test');
    //     // $purchaseOrder->setCity('test');
    //     $purchaseOrder->setCreatedAt(new \DateTime());
    //     $em->persist($purchaseOrder);
    //     $em->flush();
    //     $session->remove('cart');

    //     $this->addFlash('success', 'Commande validée avec succes , un mail va être envoyé');

    //     return $this->redirectToRoute('cart_index');
    // }



}
