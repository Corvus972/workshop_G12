<?php

namespace App\Controller;

use App\Entity\OrderItems;
use App\Repository\OrderItemsRepository;
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
                $orderNotPayed = $orders[$key]; //TODO verify only one order
            }
        }
        return $orderNotPayed;
    }
    public function setItem($item, $type) {
        if ($type === "add") {
            $old_qty = $item -> getQuantity();
            $pr = $item -> getPrice();
            $oldTotal = $item -> getTotalPrice();
            $item -> setQuantity($old_qty + 1);
            $item -> setTotalPrice($oldTotal + $pr);
        } else {
            $old_qty = $item -> getQuantity();
            $pr = $item -> getPrice();
            $oldTotal = $item -> getTotalPrice();
            $item -> setQuantity($old_qty - 1);
            $item -> setTotalPrice($oldTotal - $pr);
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
        $prods = $orderNotPayed -> getOrderItems();

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
     * @return Response
     */
    public function add($id, UserInterface $userProfile, OrderItemsRepository $orderItems): Response
    {
        $item = $orderItems -> findOneBy(['id' => $id]);
        $this -> setItem($item, "add");
        $orderNotPayed = $this-> getOrder($userProfile);
        $old_price = $orderNotPayed -> getTotalprice();
        $orderNotPayed -> setTotalprice($old_price + $item ->getPrice());

        $orderNotPayed = $this-> getOrder($userProfile);
        $prods = $orderNotPayed -> getOrderItems();


        return $this->render('cart/index.html.twig', [
            'cart_products' => $prods,
            'order' => $orderNotPayed
        ]);
    }

    /**
     * @Route("/less/{id}", name="cart_less")
     * @param $id
     * @param UserInterface $userProfile
     * @param OrderItemsRepository $orderItems
     * @return Response
     */
    public function less($id, UserInterface $userProfile, OrderItemsRepository $orderItems): Response
    {
        $item = $orderItems -> findOneBy(['id' => $id]);
        $this ->  setItem($item, "less");
        $orderNotPayed = $this-> getOrder($userProfile);
        $old_price = $orderNotPayed -> getTotalprice();
        $orderNotPayed -> setTotalprice($old_price + $item ->getPrice());

        $orderNotPayed = $this-> getOrder($userProfile);
        $prods = $orderNotPayed -> getOrderItems();

        return $this->render('cart/index.html.twig', [
            'cart_products' => $prods,
            'order' => $orderNotPayed
        ]);
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

        $em -> remove($item);
        $em -> flush();

        $orderNotPayed = $this-> getOrder($userProfile);

        $prods = $orderNotPayed -> getOrderItems();
        return $this->render('cart/index.html.twig', [
            'cart_products' => $prods,
            'order' => $orderNotPayed
        ]);
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
        return $this->render('cart/index.html.twig', [
            'cart_products' => $prods,
        ]);
    }
}
