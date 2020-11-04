<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CartProducerController extends AbstractController
{
    /**
     * @Route("/cart/producer", name="cart_producer")
     * @param Request $request
     * @param UserInterface $user
     */
    public function cartProducer(Request $request, UserInterface $userProfile): Response
    {
        return $this->render('cart_producer/index.html.twig', [
            'controller_name' => 'CartProducerController',
        ]);
    }
}
