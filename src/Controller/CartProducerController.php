<?php

namespace App\Controller;

use App\Entity\Pack;
use App\Repository\PackRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function cartProducer(
        Request $request,
        UserInterface $userProfile,
        PackRepository $packRepository,
        EntityManagerInterface $manager
        ): Response
    {
        if($userProfile->getType() === 'producer') {
            $id = $request->request->get('id', null);

            /*$pack = new Pack;
            $name = $packRepository->findName($id);
            $description = $packRepository->findDescription($id);
            $quantity = $packRepository->findQuantity($id);

            $pack->setDescription($description)
                        ->setName($name)
                        ->setQuantity($quantity);

            $manager->persist($pack);
            $manager->flush();
*/
            return $this->render('cart_producer/index.html.twig', [
                'user' => $userProfile
            ]);
        }
    }
}
