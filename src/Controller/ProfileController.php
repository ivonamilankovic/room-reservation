<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ProfileFormType;
use App\Repository\MeetingRepository;
use App\Repository\UserInMeetingRepository;
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

    /**
     * @Route("/profile/meeting_requests", name="app_user_meeting_requests")
     */
    public function meetingRequests(UserInMeetingRepository $repository):Response
    {
        $meetings = $repository->findRequestsForMeetings($this->getUser()->getId());

        return $this->render('profile/meetingRequests.html.twig',[
            'meetings' => $meetings,
        ]);
    }

    /**
     * @Route("/profile/meeting_requests/accept/{uim_id}", name="app_user_meeting_accept")
     */
    public function acceptingMeeting(EntityManagerInterface $em, UserInMeetingRepository $repository, int $uim_id):Response
    {
        $user = $repository->findOneBy(['id' => $uim_id]);
        $user->setIsGoing(true);
        $em->persist($user);
        $em->flush();

        $meetings = $repository->findAllFutureMeetings($this->getUser()->getId());

        $this->addFlash('success', 'Sastanak je prihvacen! Sve sastanke pogledajte ispod.');

        return $this->render('profile/futureMeetings.html.twig', [
            'meetings' => $meetings,
        ]);
    }

    /**
     * @Route("/profile/meeting_requests/decline/{uim_id}", name="app_user_meeting_decline")
     */
    public function declineMeeting(EntityManagerInterface $em, UserInMeetingRepository $repository, int $uim_id):Response
    {
        $user = $repository->findOneBy(['id' => $uim_id]);
        $user->setDeclined(true);
        $em->persist($user);
        $em->flush();

        $meetings = $repository->findAllFutureMeetings($this->getUser()->getId());

        $this->addFlash('success', 'Sastanak je odbijen! Sve sastanke pogledajte ispod.');

        return $this->render('profile/futureMeetings.html.twig', [
            'meetings' => $meetings,
        ]);
    }

    /**
     * @Route("/profile/all_future_meetings", name="app_user_future_meetings")
     */
    public function allFutureMeetings(UserInMeetingRepository $repository):Response
    {
        $meetings = $repository->findAllFutureMeetings($this->getUser()->getId());

        return $this->render('profile/futureMeetings.html.twig', [
            'meetings' => $meetings,
        ]);
    }

    /**
     * @Route("/profile/my_created_meetings", name="app_user_created_meetings")
     */
    public function usersCreatedMeetings(MeetingRepository $repository):Response
    {
        $meetings = $repository->findMyCreatedMeetings($this->getUser()->getId());

        return $this->render('profile/meetingsFromUser.html.twig',[
            'meetings' => $meetings,
        ]);
    }
    //TODO otkazivanje svojih kreiranih sastanaka

}