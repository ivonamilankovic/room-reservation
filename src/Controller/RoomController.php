<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\Room;
use App\Entity\UserInMeeting;
use App\Form\MeetingFormType;
use App\Form\RoomFormType;
use App\Repository\MeetingRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;

class RoomController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("rooms/showAll", name="app_showall_room")
     */
    public function showAll(RoomRepository $rep):Response
    {

        $rooms = $rep->findAll();
        //$rooms = $rep->findByCity($city); //custom query
        //$rooms = $rep->findBy([], ['id'=>'DESC']); //newest added

        if(!$rooms){
            throw $this->createNotFoundException(sprintf('No rooms found!'));
        }

        return $this->render('room/showAll.html.twig', [
            'rooms' => $rooms
        ]);
    }

    /**
     * @Route("rooms/showAll/{city}", name="app_showbycity_room")
     */
    public function showByCity(RoomRepository $rep, string $city):Response
    {
        $text = (new UnicodeString($city))
            ->replace('+', ' ');

        if($city){
           $rooms = $rep->findByCity($text); //custom query
        }

        if(!$rooms){
            throw $this->createNotFoundException(sprintf('No rooms found!'));
        }

        return $this->render('room/showAll.html.twig', [
            'rooms' => $rooms
        ]);
    }

    /**
     * @Route("rooms/{id}", name="app_showbyid_room")
     */
    public function showById(RoomRepository $roomRep, MeetingRepository $meetingRep, int $id, EntityManagerInterface $em, Request $request):Response
    {
        $room = $roomRep->findOneBy(['id'=> $id]);

        if(!$room){
            throw $this->createNotFoundException('Room not found!');
        }

        if($this->getUser()){
            $form = $this->createForm(MeetingFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                /** @var Meeting $meeting */
                $meeting = $form->getData();
                $meeting->setCreator($this->getUser());
                $meeting->setRoom($room);

                //TODO provera da li je soba zauzeta u to vreme

                $users = $form->get('users')->getData();
                foreach ($users as $user){
                    //svaki user koji je ubacen na sastanak
                    $userInMeeting = new UserInMeeting();
                    $new = $userInMeeting->addUser($user);

                    //TODO provera da li je user zauzet za neki drugi sastanak --> bolje preko ajaxa?
                    //ako je zauzet bice rezultata, ako ne nece
                    $m = $meetingRep->findByIsUserOnAnotherMeeting($form->get('start')->getData(),$form->get('end')->getData(),$user->getId());

                    if(!$m){
                        $meeting->addUserInMeeting($new);
                        $em->persist($userInMeeting);
                    }
                    else{
                        //gde da ispisujem zauzete?
                    }

                }
                $userInMeeting = new UserInMeeting();
                $new = $userInMeeting->addUser($this->getUser()); //i kretor ide na sastanak
                $new->setIsGoing(true);
                $meeting->addUserInMeeting($new);
                $em->persist($userInMeeting);
                $em->persist($meeting);
                $em->flush();

                $this->addFlash('success','Nova rezervacija sale za sastanak je kreirana!');

                $this->redirectToRoute('app_showbyid_room', [
                    'id' => $room->getId()
                ]);

            }
            return $this->render('room/showOne.html.twig', [
                'room' => $room,
                'form' => $form->createView()
            ]);
        }

        return $this->render('room/showOne.html.twig', [
            'room' => $room,
            'form' => null
        ]);
    }

}