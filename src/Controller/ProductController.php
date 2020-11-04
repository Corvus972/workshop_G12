<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository): Response
    {
        $catalogue = $productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $catalogue,
        ]);
    }
}
