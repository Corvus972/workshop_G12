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
use Vicopo\Vicopo;
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
        $userZipCode = $userProfile -> getRegion();
        $getTwoNumberOfZip = substr($userZipCode, 0, 2);
        $farmerInfo = $userRepository -> findBy(['region' => $userZipCode]);
        $productsFilterByZipCode = $productRepository -> findBy((['user' => $farmerInfo]));
        $packFilterByZipCode = $packRepository -> findBy((['id' => $farmerInfo]));

        if ($req){
            $prods = new ArrayCollection($productsFilterByZipCode);
            $criteria = Criteria::create()->where(Criteria::expr()->contains('name', $req));
            $catalogue = $prods->matching($criteria);
        } else {
            $catalogue = $productsFilterByZipCode;
        }

        return $this->render('product/index.html.twig', [
            'packs' => $packFilterByZipCode,
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

        $order->setUser($user)
            ->setDate(New \Datetime)
            ->setPaymentMethod('Pas encore payé')
            ->setShipped(false)
            ->setStatus('Non payé');

        $order_item->setQuantity($quantity)
            ->setUnit($unit)
            ->setPrice($price)
            ->setOrderId($order)
            ->setProductRef($productRef);

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
