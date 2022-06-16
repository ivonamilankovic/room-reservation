<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Entity\Room;
use App\Form\MeetingFormType;
use App\Form\RoomFormType;
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
    public function showById(RoomRepository $rep, int $id, EntityManagerInterface $em, Request $request):Response
    {

        $room = $rep->findOneBy(['id'=> $id]);

        if(!$room){
            throw $this->createNotFoundException(sprintf('Room not found!'));
        }


        if($this->getUser()){
            $form = $this->createForm(MeetingFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                /** @var Meeting $meeting */
                $meeting = $form->getData();

                //treba izmene oko unosa

                $em->persist($meeting);
                $em->flush();

                $this->addFlash('success','Nova rezervacija sale za sastanak je kreirana!');

                $this->redirectToRoute('app_showbyid_room');

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