<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartProducerController extends AbstractController
{
    /**
     * @Route("/cart/producer", name="cart_producer")
     */
    public function index(): Response
    {
        return $this->render('cart_producer/index.html.twig', [
            'controller_name' => 'CartProducerController',
        ]);
    }
}
