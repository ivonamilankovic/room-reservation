<?php

namespace App\Controller;

use App\Entity\Room;
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
     * @Route("admin/rooms/insert", name="admin_insert_room")
     */
    public function insert(EntityManagerInterface $em, Request $request):Response
    {

        $form = $this->createForm(RoomFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var Room $room */
            $room = $form->getData();

            $em->persist($room);
            $em->flush();

            $this->addFlash('success','Nova sala je kreirana!');

            $this->redirectToRoute('app_showall_room');

        }

        return $this->render('admin/room/new.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/rooms/edit/{id}", name="admin_edit_room")
     */
    public function edit(EntityManagerInterface $em, Request $request, Room $room):Response
    {
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->persist($room);
            $em->flush();

            $this->addFlash('success','Upesno ste izmenili podatke o sali!');

            $this->redirectToRoute('admin_edit_room', [
                'id' =>$room->getId()
            ]);

        }

        return $this->render('admin/room/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

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
    public function showById(RoomRepository $rep, int $id):Response
    {

        $room = $rep->findOneBy(['id'=> $id]);

        if(!$room){
            throw $this->createNotFoundException(sprintf('Room not found!'));
        }

        return $this->render('room/showOne.html.twig', [
            'room' => $room
        ]);
    }

}