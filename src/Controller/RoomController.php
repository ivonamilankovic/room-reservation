<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\UserInMeeting;
use App\Form\MeetingFormType;
use App\Form\SearchRoomByCityFormType;
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
    public function showById(RoomRepository $roomRep, MeetingRepository $meetingRep, int $id, EntityManagerInterface $em, Request $request):Response
    {
        $room = $roomRep->findOneBy(['id'=> $id]);

        if(!$room){
            throw $this->createNotFoundException('Room not found!');
        }

        if($this->getUser()){
            $form = $this->createForm(MeetingFormType::class, null, [
                'loggedUser' => $this->getUser()->getId()
            ]);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                /**@var Meeting $meeting */
                $meeting = $form->getData();
                $meeting->setCreator($this->getUser());
                $meeting->setRoom($room);

                $isRoomTaken = $meetingRep->findByIsRoomTakenForAnotherMeeting(
                    $form->get('start')->getData(),
                    $form->get('end')->getData(),
                    $room->getId(),
                );

                //TODO nekako prikazati kada je soba zauzeta?
                //provera da li je soba zauzeta u dato vreme
                if($isRoomTaken){
                    //$this->addFlash('success', 'Soba je zauzeta u odabrano vreme! Sastanak nije sacuvan.');
                    //zasto ne ispise ako je error ili warning type
                    return $this->render('room/showOne.html.twig', [
                        'room' => $room,
                        'form' => $form->createView(),
                        'error_msg' => 'Soba je zauzeta u odabrano vreme! Sastanak nije sacuvan.',
                    ]);
                }

                //provera da li ima vise oznacenih osoba nego sto je kapacitet
                $userForMeeting = $form->get('users')->getData();
                if(count($userForMeeting) > $room->getSeatNumber()){
                    //dd(count($userForMeeting));
                    return $this->render('room/showOne.html.twig', [
                        'room' => $room,
                        'form' => $form->createView(),
                        'error_msg' => 'Odabrali ste vise osoba nego sto je kapacitet sobe! Sastanak nije sacuvan.',
                    ]);
                }

                //dodavanje svakoga u sastanak sa defaultom isGoing = 0
                $errorMsg = "Osobe: ";

                foreach ($userForMeeting as $user){
                    //provera da li je osoba na drugom sastanku u dato vreme
                    //TODO ako je user zauzet da pita da li ipak zelimo da ga doda
                    $isPersonBusy = $meetingRep->findByIsUserOnAnotherMeeting(
                        $form->get('start')->getData(),
                        $form->get('end')->getData(),
                        $user->getId(),
                    );

                    if($isPersonBusy){
                        $errorMsg .= $user->getFullName(). ", ";
                    }else {
                        $userInMeeting = new UserInMeeting();
                        $userInMeeting->setUser($user);
                        $userInMeeting->setMeeting($meeting);
                        $em->persist($userInMeeting);
                    }
                }

                if($errorMsg !== "Osobe: "){
                    return $this->render('room/showOne.html.twig', [
                        'room' => $room,
                        'form' => $form->createView(),
                        'error_msg' => $errorMsg.' su u to vreme na drugom sastanku. Sastanak nije sacuvan.',
                    ]);
                }

                //za kreatora se podrazumeva da prisustvuje
                $creatorInMeeting = new UserInMeeting();
                $creatorInMeeting->setUser($this->getUser());
                $creatorInMeeting->setMeeting($meeting);
                $creatorInMeeting->setIsGoing(true);

                $em->persist($meeting);
                $em->persist($creatorInMeeting);
                $em->flush();

                $this->addFlash('success', 'Uspesno ste kreirali novi sastanak!');

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

}