<?php

namespace App\Controller;

use App\Entity\Pack;
use App\Entity\Product;
use App\Repository\PackRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartProducerController extends AbstractController
{
    /**
     * @Route("/cart/producer", name="cart_producer")
     * @param Request $request
     * @param UserInterface $user
     */
    public function cartProducerPage(Request $request, UserInterface $user, ProductRepository $repo, EntityManagerInterface $manager)
    {

        $lotsRefs = $repo->findLotsRefs();
        $lots = [];
        $products = $repo->findUserAvailableproducts($user->getId());

        $prices = [];
        $quantities = [];

        // Reconstitue lots with products pack ref
        foreach($lotsRefs as $key => $value) {
            foreach($value as $tab) { 
                $refs = $repo->findProductsByLotRef($tab);
                $lots[] = $refs;
                $prices[] = $repo->findPricesByLotRef($tab);
                $quantities[] = $repo->findQuantitiesByLotRef($tab);
            }
        }

            return $this->render('cart_producer/index.html.twig', [
                'products'   => $products,
                'lots'       => $lots
            ]);

    }

    /**
     * @Route("/cart/producer/add", name="add_cart_producer")
     * @param Request $request
     * @param UserInterface $user
     */
    public function cartProducer(Request $request, UserInterface $user, PackRepository $packRepository, EntityManagerInterface $manager)
    {
            $name = $request->request->get('name', null);
            $quantity = $request->request->get('quantity');
            $unit = $request->request->get('unit');
            $productRef = $request->request->get('productref');
            $price = $request->request->get('price');


            $product = new Product;
            $product->setName($name)
                    ->setQuantity($quantity)
                    ->setUnit($unit)
                    ->setPrice($price)
                    ->setProductRef($productRef)
                    ->setUser($user);
        
            $pack = new Pack();
            $pack->addProduct($product);

            $manager->persist($product);
            $manager->flush();

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "Produit ajouté au panier",
                ]
            );
            return $this->render('cart_producer/index.html.twig');
            }

    /**
     * @Route("/cart/producer/add-lot", name="add_lots_producer")
     * @param Request $request
     * @param UserInterface $user
     */
    public function LotsProducer(Request $request, UserInterface $user, ProductRepository $repo, EntityManagerInterface $manager)
    {
        $ids  = $request->request->get('ids', null);
        $ref = $request->request->get('ref', null);

        foreach($ids as $key => $value) {
    
            $product = $repo->findProduct((int)$value);
            $product->setPackRef($ref);

            $manager->persist($product);
            $manager->flush();

        }

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "Produit ajouté au panier",
                ]
            );
    }        
}
