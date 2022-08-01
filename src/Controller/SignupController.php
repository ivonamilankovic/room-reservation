<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SignupController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request, MailerInterface $mailer, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, Security $security, VerifyEmailHelperInterface $verifyEmailHelper):Response
    {

        if ($security->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(SignupFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $hasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $user->setRoles(array('ROLE_USER'));

            $em->persist($user);
            $em->flush();

            $signature = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id'=>$user->getId()]
            );

            $email = (new TemplatedEmail())
                ->from(new Address('example@example.com','Name name'))
                ->to(new Address($user->getEmail(), $user->getFullName()))
                ->subject('Verifikacija naloga')
                ->htmlTemplate('email/verifyEmail.html.twig')
                ->context([
                    'ver_link' => $signature->getSignedUrl(),
                ])
            ;

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                dd($e->getDebug());
            }

            $this->addFlash('success', 'Tvoj profil je uspešno kreiran! Email za potvrdu naloga je poslat.');

            return $this->redirectToRoute('app_home');

        }

        return $this->render("security/signup.html.twig",[
            'signup_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify", name="app_verify_email")
     */
    public function verifyEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $em, VerifyEmailHelperInterface $emailHelper)
    {
        $user = $userRepository->find($request->query->get('id'));
        if(!$user){
            throw $this->createNotFoundException('User not found');
        }

        try{
            $emailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        }catch (VerifyEmailExceptionInterface $e){
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_signup');
        }

        $user->setIsVerified(true);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success','Uspešno ste verifikovali vas nalog. Sada se možete prijaviti!');

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/verify/resend/{id}", name="app_verify_resend_email")
     */
    public function resendVerifyEmail(VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer, UserRepository $userRepository, int $id):Response
    {
        $user = $userRepository->findOneBy(['id'=> $id]);

        $signature = $verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id'=>$user->getId()]
        );

        $email = (new TemplatedEmail())
            ->from(new Address('example@example.com','Name name'))
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->subject('Verifikacija naloga')
            ->htmlTemplate('email/verifyEmail.html.twig')
            ->context([
                'ver_link' => $signature->getSignedUrl(),
            ])
        ;

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Novi mejl za potvrdu naloga je poslat!');
        } catch (TransportExceptionInterface $e) {
            dd($e->getDebug());
        }


        return $this->render('security/resendVerifyEmail.html.twig',[
            'user' => $user
        ]);
    }

    /**
     * @Route("/new_verify/{id}", name="app_new_verify")
     */
    public function newVerify(UserRepository $userRepository, int $id):Response
    {
        $user = $userRepository->findOneBy(['id'=> $id]);

        return $this->render('security/resendVerifyEmail.html.twig',[
            'user' => $user
        ]);
    }

}