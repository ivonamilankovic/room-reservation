<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignupController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher){

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(SignupFormType::class);
dd($request->request->get('firstName'));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /** @var User $user */
            $user = $form->getData();

            $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $user->setRole('user');

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Tvoj profil je uspesno kreiran!');

            return $this->redirectToRoute('app_home');

        }

        return $this->render("security/signup.html.twig",[
            'signup_form' => $form->createView(),
        ]);
    }

}