<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItems;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\User;

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

     /**
     * @Route("/add_to_cart", name="add_to_cart")
     * @param ProductRepository $productRepository
     * @param UserInterface $user
     * @return Response
     */
    public function addToCart(Request $request, ProductRepository $repository, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        
        $id = $request->request->get('id', null);
        $target = $repository->findById($id);

        $order_item = new OrderItems();
        $order = new Order();

        $quantity = $repository->findQuantity($id); 
        $unit = $repository->findUnit($id);
        $price = $repository->findPrice($id);
        $productRef = $repository->findProductRef($id);

        $order_item->setQuantity($quantity)
                   ->setUnit($unit)
                   ->setPrice($price)
                   ->setProductRef($productRef);
        
        $order->setUser($user)
              ->setDate(New \Datetime)
              ->setPaymentMethod('Pas encore payé')
              ->setShipped(false)
              ->setStatus('Non payé');

        $manager->persist($order_item);
        $manager->flush($order_item);

        $manager->persist($order);
        $manager->flush($order);
        
        return new JsonResponse(
            [
                'success' => true,
                'message' => "Produit ajouté au panier",
            ]
        );

        return $this->render('product/index.html.twig');
    }
    
}
