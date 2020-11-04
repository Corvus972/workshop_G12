<?php

namespace App\Controller;

use App\Entity\Product;
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


class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function index(ProductRepository $productRepository, Request $request, UserInterface $userProfile, UserRepository $userRepository): Response
    {

//        $userProfile -> getRegion();
        $req = $request->query->get('q');
        $userRegion = $userProfile -> getRegion();
        $farmerInfo = $userRepository -> findBy(['region' => $userRegion]);
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
            'products' => $catalogue,
        ]);
    }
}
