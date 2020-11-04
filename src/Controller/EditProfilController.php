<?php

namespace App\Controller;

use App\Form\EditProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EditProfilController extends AbstractController
{
    /**
     * @Route("edit/profil", name="edit_profil")
     * @param Request $request
     * @param UserInterface $user
     */
    public function editProfil(Request $request, UserInterface $userProfile, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(EditProfilType::class,$userProfile); //On utiliser le userProfile pour générer le formulaire pré-rempli
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Pour encoder le password quand il est modifié
            $hash = $encoder->encodePassword($userProfile, $userProfile->getPassword());
            $userProfile->setPassword($hash);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userProfile);
            $entityManager->flush();
            return $this->redirectToRoute('profil');
        }
        if($userProfile->getType() === 'consumer') {
            return $this->render('edit_profil/edit_profil_consumer.twig', [
                'user' => $userProfile,
                'form' => $form->createView()
            ]);
        }else{
            return $this->render('edit_profil/edit_profil_producer.twig', [
                'user' => $userProfile,
                'form' => $form->createView()
            ]);
        }
    }
    /**
     * @Route("profil", name="profil")
     * @param Request $request
     * @param UserInterface $user
     */
    public function displayProfil(Request $request, UserInterface $userProfile, UserPasswordEncoderInterface $encoder): Response
    {
        if($userProfile->getType() === 'consumer') {
            return $this->render('edit_profil/page_profil_consumer.twig', [
                'user' => $userProfile
            ]);
        }else{
            return $this->render('edit_profil/page_profil_producer.twig', [
                'user' => $userProfile
            ]);
        }
    }

}
