<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;

class RoomController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("rooms/insert", name="app_insert_room")
     */
    public function insert(EntityManagerInterface $entityManager):Response
    {
        $room = new Room();

        $room->setName('hundred and x')
            ->setSeatNumber(5)
            ->setCity('Novi sad')
            ->setStreet('ulica');

        $entityManager->persist($room);
        $entityManager->flush();

        return new Response(sprintf('inserted with id #%d', $room->getId()));
    }

    /**
     * @Route("rooms/edit", name="app_edit_room")
     */
    public function edit(EntityManagerInterface $entityManager):Response
    {
        $room = new Room();



        $entityManager->persist($room);
        $entityManager->flush();

        return new Response(sprintf('editing '));
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