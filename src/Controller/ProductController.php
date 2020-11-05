<?php

namespace App\Controller;


use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\PackRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use App\Entity\OrderItems;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;



/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/index", name="product_index")
     * @param ProductRepository $productRepository
     * @param Request $request
     * @param UserInterface $userProfile
     * @param UserRepository $userRepository
     * @param PackRepository $packRepository
     * @return Response
     * @throws Exception
     */

    public function index(ProductRepository $productRepository, Request $request, UserInterface $userProfile, UserRepository $userRepository, PackRepository $packRepository): Response
    {
        $req = $request->query->get('q');
        $userRegion = $userProfile -> getRegion();
        $farmerInfo = $userRepository -> findBy(['region' => $userRegion]);

        $packFilterByRegion = $packRepository -> findBy((['id' => $farmerInfo]));

        $productsFilterByRegion = $productRepository -> findBy((['user' => $farmerInfo]));
      
//        dump($userRegion);
        if ($req){
            $prods = new ArrayCollection($productsFilterByRegion);
            $criteria = Criteria::create()->where(Criteria::expr()->contains('name', $req));
            $catalogue = $prods->matching($criteria);
        } else {
            $catalogue = $productsFilterByRegion;
        }

        return $this->render('product/index.html.twig', [
            'packs' => $packFilterByRegion,
            'products' => $catalogue,
        ]);
    }


    /**
     * @Route("/recipe", name="product_recipe")
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */

    public function recipe(OrderRepository $orderRepository, Request $request, ProductRepository $productRepository): Response
    {

//        $userProfile -> getRegion();
        $req = $request->query->get('q');
        $recipes = $orderRepository -> findOneBy(['id' => $req]);

        return $this->render('product/recipe.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * @Route("/add_to_cart", name="add_to_cart")
     * @param Request $request
     * @param ProductRepository $repository
     * @param EntityManagerInterface $manager
     * @param OrderRepository $orderRepository
     * @param UserInterface $userProfile
     * @return Response
     */
    public function addToCart(Request $request, ProductRepository $repository, EntityManagerInterface $manager, OrderRepository $orderRepository, UserInterface $userProfile)
    {
        $user = $this->getUser();

        $id = $request->request->get('id', null);
        $target = $repository->findById($id);



        $orders = $userProfile -> getOrders();
        $orderNotPayed = null;
        foreach ($orders as $key => $val){
            if($orders[$key]->getStatus() ===  "Non payé"){
                $orderNotPayed = $orders[$key];
            }
        }

//        $quantity = $repository->findQuantity($id);
        $unit = $repository->findUnit($id);
        $price = $repository->findPrice($id);
        $productRef = $repository->findProductRef($id);
        $name = $repository->findName($id);
        $img= $repository->findImg($id);

        if($orderNotPayed){ //ORDER NOT PAYED
            $order = $orderNotPayed;
            $old_total = $order -> getTotalPrice();
            $order->setTotalprice($price  + $old_total)
                ->setUser($user)
                ->setDate(New \Datetime)
                ->setPaymentMethod('Pas encore payé')
                ->setShipped(false)
                ->setStatus('Non payé');
            $manager->persist($order);
            $manager->flush();

        } else{
            $order = new Order(); //ORDER NOT PAYED
            $order->setTotalprice($price)
                ->setUser($user)
                ->setDate(New \Datetime)
                ->setPaymentMethod('Pas encore payé')
                ->setShipped(false)
                ->setStatus('Non payé');
            $manager->persist($order);
            $manager->flush();
        }

        $items = $orderNotPayed -> getOrderItems();
//        $order_item = null;
//        if($items) {
//            foreach($items as $key => $val){
//                if($items[$key]-> getProductRef() === $productRef ) {
//                    $order_item = $items[$key];
//                    $old_qty = $order_item-> getQuantity();
//                    $old_tot_price = $order_item-> getTotalPrice();
//                    $order_item->setQuantity($old_qty + 1)
//                        ->setUnit($unit)
//                        ->setPrice($price)
//                        ->setTotalPrice($old_tot_price + $price)
//                        ->setOrderId($order)
//                        ->setName($name)
//                        ->setImage($img)
//                        ->setProductRef($productRef);
//                }
//            }

//        }else{
        $order_item = new OrderItems();
        $order_item->setQuantity(1)
            ->setUnit($unit)
            ->setPrice($price)
            ->setTotalPrice($price)
            ->setOrderId($order)
            ->setName($name)
            ->setImage($img)
            ->setProductRef($productRef);
//        }


        $manager->persist($order_item);
        $manager->flush();




        return new JsonResponse(
            [
                'success' => true,
                'message' => "Produit ajouté au panier",
            ]
        );

        return $this->render('product/index.html.twig');
    }


}
