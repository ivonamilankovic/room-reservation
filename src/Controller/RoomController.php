<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\UserInMeeting;
use App\Form\MeetingFormType;
use App\Form\SearchRoomByCityFormType;
use App\Repository\MeetingRepository;
use App\Repository\RoomRepository;
use App\Service\MeetingChecksHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class RoomController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("rooms/showAll", name="app_showall_room")
     */
    public function showAll(RoomRepository $rep, Request $request):Response
    {

        $rooms = $rep->findAll();
        //$rooms = $rep->findByCity($city); //custom query
        //$rooms = $rep->findBy([], ['id'=>'DESC']); //newest added

        if(!$rooms){
            throw $this->createNotFoundException(sprintf('No rooms found!'));
        }

        $form = $this->createForm(SearchRoomByCityFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $searchedCity = $form->get('search')->getData();
            return $this->redirectToRoute('app_showbycity_room', [
                'city' => $searchedCity
            ]);
        }

        return $this->render('room/showAll.html.twig', [
            'rooms' => $rooms,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("rooms/showAll/{city}", name="app_showbycity_room")
     */
    public function showByCity(RoomRepository $rep, string $city):Response
    {

        if($city){
           $rooms = $rep->findByCity($city); //custom query
        }

        if(!$rooms){
            throw $this->createNotFoundException(sprintf('No rooms found!'));
        }

        return $this->render('room/showAll.html.twig', [
            'rooms' => $rooms,
            'form' => null,
        ]);
    }

    /**
     * @Route("rooms/{id}", name="app_showbyid_room")
     */
    public function showById(RoomRepository $roomRep, int $id, EntityManagerInterface $em, Request $request, MeetingChecksHelper $meetingChecksHelper):Response
    {
        $room = $roomRep->findOneBy(['id'=> $id]);

        if(!$room){
            throw $this->createNotFoundException('Room not found!');
        }

        if($this->getUser()){
            $form = $this->createForm(MeetingFormType::class, null, [
                'loggedUser' => $this->getUser()->getId(),
                'selected_room' => $room
            ]);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                /**@var Meeting $meeting */
                $meeting = $form->getData();
                $meeting->setCreator($this->getUser());
                $meeting->setRoom($room);
                $userForMeeting = $form->get('users')->getData();
                $message = $meetingChecksHelper->checkAvailability(
                    $meeting,
                    $room,
                    $this->getUser(),
                    $userForMeeting);
                if($message){
                    return $this->render('room/showOne.html.twig',[
                        'room' => $room,
                        'form' => $form->createView(),
                        'error_msg' => $message,
                    ]);
                }

                $creatorInMeeting = new UserInMeeting();
                $creatorInMeeting->setUser($this->getUser());
                $creatorInMeeting->setMeeting($meeting);
                $creatorInMeeting->setIsGoing(true);
                $em->persist($creatorInMeeting);

                foreach ($userForMeeting as $user){
                    $userInMeeting = new UserInMeeting();
                    $userInMeeting->setUser($user);
                    $userInMeeting->setMeeting($meeting);
                    $em->persist($userInMeeting);
                }

                $em->persist($meeting);
                $em->flush();

                $this->addFlash('success', 'Uspesno ste kreirali novi sastanak!');

                return $this->redirectToRoute('app_user_created_meetings');

            }

            return $this->render('room/showOne.html.twig', [
                'room' => $room,
                'form' => $form->createView(),
                'error_msg' => null,
            ]);
        }

        return $this->render('room/showOne.html.twig', [
            'room' => $room,
            'form' => null,
            'error_msg' => null,
        ]);
    }

    /**
     * @Route("/room_availability", name="app_room_availability", options={"expose" = true})
     */
    public function getRoomAvailability(MeetingRepository $meetingRep, Request $request)
    {
        $meetings = $meetingRep->findMeetingsByDateAndRoom(
            $request->request->get('id'),
            $request->request->get('date')
        );
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($meetings,'json');

        return new JsonResponse($json);
    }

    /**
     * @Route("/user_availability", name="app_user_availability", options={"expose" = true})
     */
    public function getUserAvailability(MeetingRepository $meetingRepository, Request $request)
    {
        $results = $meetingRepository->findByIsUserOnAnotherMeeting(
            $request->request->get('start'),
            $request->request->get('end'),
            $request->request->get('userID'),
            0
        );
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($results,'json');

        return new JsonResponse($json);
    }

}