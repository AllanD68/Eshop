<?php

namespace App\Controller;

use App\Form\CartType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     */
    public function cart(SessionInterface $session, ProductRepository $productRepository ) 

    {
        $cart = $session->get('cart', []);

        $cartWithData = [];



        foreach ($cart as $id => $qty) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'qty' => $qty
            ];
        }

        $total = 0;

        foreach ($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['qty'];
            $total += $totalItem;
        }

        return $this->render('cart/cart_index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    // /**
    //  * @Route("/panier/edit_remove", name="cart_edit_remove")
    //  * 
    //  */
    // public function CartEditRemove(SessionInterface $session)
    // {

    //     //Récupérer l'id en jsonDecode
    //     $cart = $session->get('cart', []);
    //     $data = [];
    //     foreach($cart as $key => $val){
    //         if($val > 1){
    //             $cart[$key] = $val-1;
    //             $data["message"] = 'Ok';
    //             $data['qty'] = $cart[$key];
    //             $data["id"] = $key;
    //             $session->set('cart', $cart);
    //         } else {
    //             $data['qty'] = $cart[$key];
    //             $data["message"] = 'Oops';
    //         }
    //     }

        
    //     $response = new Response(
    //         json_encode([
    //             'data' => $data,
    //         ]),
    //         Response::HTTP_OK,
    //         ['content-type' => 'application/json']
    //     );
    //     return $response;
    // }

    //  /**
    //  * @Route("/panier/edit_add", name="cart_edit_add")
    //  * 
    //  */
    // public function cartEditAdd(SessionInterface $session)
    // {

    //     //Récupérer l'id en jsonDecode
    //     $cart = $session->get('cart', []);
    //     $data = [];
        
    //     foreach($cart as $key => $val){
    //         if($val <= 15){
    //             $cart[$key] = $val+1;
    //             $data["message"] = 'Ok';
    //             $data['qty'] = $cart[$key];
    //             $data["id"] = $key;
    //             $session->set('cart', $cart);
    //         } else {
    //             $data['qty'] = $cart[$key];
    //             $data["message"] = 'Oops';
    //         }
    //     }

        
    //     $response = new Response(
    //         json_encode([
    //             'data' => $data,
    //         ]),
    //         Response::HTTP_OK,
    //         ['content-type' => 'application/json']
    //     );
    //     return $response;
    // }

    /**
     * @Route("/panier/ajout/{id}", name="cart_add")
     */
    public function addCart($id, SessionInterface $session , Request $request)
    {

        //on regarde dans la session si il y a un panier sinon on en créé un
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {

            $cart[$id] = 1;
        }

        // $cart = $this->$request->getSession();

        // if (array_key_exists($id , $cart)) {
        //     if ($this->$request->query->get('qty') != null){
        //     $cart['id'] = $this->$request->query->get('qty');
        //     }
        // } elseif ($this->$request->query->get('qty') != null) {
        //     $cart[$id] = $this->$request->query->get('qty');
        // }
        // else {
        //     $cart[$id] = 1;
        // }
 


        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier');

        return $this->redirectToRoute("product_list");
    }




    /**
     * @Route("/panier/ajout/show/{id}", name="cart_add_show")
     */
    public function addCartShow($id, SessionInterface $session , Request $request)
    {

        //on regarde dans la session si il y a un panier sinon on en créé un
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {

            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier');
        

        return $this->redirect($this->generateUrl('product_show', array('id' => $id )));

    }
    /**
     * @Route("/panier/surpprimer/{id}" , name="cart_remove")
     */

    public function removeCart($id, SessionInterface $session)
    {

        $cart = $session->get('cart');

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute("cart_index");
    }

     /**
     * @Route("/panier/surpprimer" , name="cart_remove_full")
     */
    public function removeCartFull( SessionInterface $session)
    {

        $cart = $session->get('cart');

        if (!empty($cart)) {
            $session->remove('cart');
        }

        return $this->redirectToRoute("cart_index");
    }

   
}
