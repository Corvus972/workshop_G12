<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){ 
            $hash = $encoder->encodePassword($user, $user->getPassword()); 
            $user->setPassword($hash);
            if($user->getType() === 'consumer'){
                $user->setRoles(["ROLE_USER"]);
            }else{
                $user->setRoles(["ROLE_PRODUCER"]);
            }
            $manager->persist($user);
            
            $manager->flush();

            $this->addFlash('success', 'Votre compte à bien été enregistré.');
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
