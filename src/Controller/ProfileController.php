<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class ProfileController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/profile/user", name="app_user_profile")
     */
    public function profileUser(Request $request,  EntityManagerInterface $em):Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Vasi podaci su uspesno izmenjeni');
            $this->redirect($request->getUri());
        }

        return $this->render('profile/userProfile.html.twig',
        [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/change_password", name="app_user_change_password")
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher):Response
    {
        /**
         * @var PasswordAuthenticatedUserInterface $user
         */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $hasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Vasa lozinka je uspesno promenjena.");
            $this->redirect($request->getUri());
        }

        return $this->render('profile/changePassword.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

}