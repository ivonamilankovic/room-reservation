<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SignupController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, Security $security):Response
    {

        if ($security->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(SignupFormType::class, $user);
        $form->handleRequest($request);
        //dd($request); //ne uzma pass u form
        if($form->isSubmitted() && $form->isValid()){
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $hasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $user->setRoles(array('ROLE_USER'));

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