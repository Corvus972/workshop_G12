<?php

namespace App\Controller;

use App\Entity\OrderItems;
use App\Repository\OrderItemsRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    public function getOrder($userProfile) {
        $orders = $userProfile -> getOrders();
        $orderNotPayed = null;
        foreach ($orders as $key => $val){
            if($orders[$key]->getStatus() ===  "Non payé"){
                $orderNotPayed = $orders[$key];
            }
        }
        return $orderNotPayed;
    }
    public function setItem($item, $type, $em) {
        if ($type === "add") {
            $old_qty = $item -> getQuantity();
            $pr = $item -> getPrice();
            $oldTotal = $item -> getTotalPrice();
            $item -> setQuantity($old_qty + 1);
            $item -> setTotalPrice($oldTotal + $pr);
            $em -> flush();
        } else {
            $old_qty = $item -> getQuantity();
            if($old_qty > 1) {
                $pr = $item -> getPrice();
                $oldTotal = $item -> getTotalPrice();
                $item -> setQuantity($old_qty - 1);
                $item -> setTotalPrice($oldTotal - $pr);
                $em -> flush();
            }
        }

    }
    /**
     * @Route("/index", name="cart_index")
     * @param UserInterface $userProfile
     * @return Response
     */
    public function index(UserInterface $userProfile): Response
    {
        $orderNotPayed = $this-> getOrder($userProfile);
        $prods = null;
        if($orderNotPayed) $prods = $orderNotPayed -> getOrderItems();

        return $this->render('cart/index.html.twig', [
            'cart_products' => $prods,
            'order' => $orderNotPayed
        ]);
    }

    /**
     * @Route("/add/{id}", name="cart_add")
     * @param $id
     * @param UserInterface $userProfile
     * @param OrderItemsRepository $orderItems
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function add($id, UserInterface $userProfile, OrderItemsRepository $orderItems, EntityManagerInterface $em): Response
    {
        $item = $orderItems -> findOneBy(['id' => $id]);
        $this -> setItem($item, "add", $em);
        $orderNotPayed = $this-> getOrder($userProfile);
        $old_price = $orderNotPayed -> getTotalprice();
        $orderNotPayed -> setTotalprice($old_price + $item ->getPrice());
        $em -> flush();

        $orderNotPayed = $this-> getOrder($userProfile);
        $prods = $orderNotPayed -> getOrderItems();

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/less/{id}", name="cart_less")
     * @param $id
     * @param UserInterface $userProfile
     * @param OrderItemsRepository $orderItems
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function less($id, UserInterface $userProfile, OrderItemsRepository $orderItems, EntityManagerInterface $em): Response
    {
        $item = $orderItems -> findOneBy(['id' => $id]);
        $this ->  setItem($item, "less", $em);
        $orderNotPayed = $this-> getOrder($userProfile);
        $old_price = $orderNotPayed -> getTotalprice();
        $orderNotPayed -> setTotalprice($old_price - $item ->getPrice());
        $em -> flush();

        $orderNotPayed = $this-> getOrder($userProfile);
        $prods = $orderNotPayed -> getOrderItems();

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/delete/{id}", name="cart_delete")
     * @param $id
     * @param UserInterface $userProfile
     * @param OrderItemsRepository $orderItems
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete($id, UserInterface $userProfile, OrderItemsRepository $orderItems, EntityManagerInterface $em): Response
    {
        $item = $orderItems -> findOneBy(['id' => $id]);

        $orderNotPayed = $this-> getOrder($userProfile);
        $old_price = $orderNotPayed -> getTotalprice();
        $orderNotPayed -> setTotalprice($old_price - $item ->getTotalPrice());
        $em -> flush();
        $em -> remove($item);
        $em -> flush();

        $orderNotPayed = $this-> getOrder($userProfile);

        $prods = $orderNotPayed -> getOrderItems();

        $this->addFlash('success', 'Produit supprimé');
        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/clear", name="cart_clear")
     * @param UserInterface $userProfile
     * @param OrderItemsRepository $orderItems
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function clear(UserInterface $userProfile, OrderItemsRepository $orderItems, EntityManagerInterface $em): Response
    {
        $orderNotPayed = $this-> getOrder($userProfile);
        $orderNotPayed -> setTotalprice(0.00);
        $prods = $orderNotPayed -> getOrderItems();
        foreach ($prods as $key => $val){
            $em -> remove($prods[$key]);
            $em -> flush();
        }
        $this->addFlash('success', 'Panier Supprimé');
        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/done", name="cart_done")
     * @param UserInterface $userProfile
     * @param OrderItemsRepository $orderItems
     * @param EntityManagerInterface $em
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function done(UserInterface $userProfile, OrderItemsRepository $orderItems, EntityManagerInterface $em, ProductRepository $productRepository): Response
    {
        $orderNotPayed = $this-> getOrder($userProfile);
        $orderNotPayed -> setStatus("Commandé");
        $em -> flush();
        $prods = $orderNotPayed -> getOrderItems();

        $prodRepo = $productRepository;
        foreach ($prods as $key => $val){
            foreach ($prodRepo as $k => $v){
                if($prodRepo[$k] -> getProductRef() === $prods[$key]) {
                    $old_qty = $prodRepo[$k] -> getQuantity();
                    $prodRepo[$k] -> setQuantity($old_qty - $prods[$key] -> getQuantity());
                    $em -> persist($prodRepo[$k]);
                    $em ->flush();
                }
            }

        }

        $this->addFlash('success', 'Votre compte a bien été passée');
        return $this->redirectToRoute('profil');
    }

}
