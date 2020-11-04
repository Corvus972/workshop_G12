<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/cart", name="cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/index", name="cart_index")
     */
    public function index(UserInterface $userProfile): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

//    /**
//     * @Route("/edit/{id}, name="cart_edit")
//     * @param $id
//     * @return Response
//     */
//    public function edit($id, UserInterface $userProfile): Response
//    {
////        $userProfile->
//
//        return $this->render('cart/index.html.twig', [
//            'controller_name' => 'CartController',
//        ]);
//    }
//
//    /**
//     * @Route("/delete/{id}, name="cart_delete")
//     * @param UserInterface $userProfile
//     * @return Response
//     */
//    public function delete($id, UserInterface $userProfile): Response
//    {
//        return $this->render('cart/index.html.twig', [
//            'controller_name' => 'CartController',
//        ]);
//    }
}
