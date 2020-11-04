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
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Vicopo\Vicopo;

/**
 * @Route("/product", name="product")
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
        $userRegion = Vicopo::https('34');
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
}
