<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/admin", name="admin_home")
     */
    public function adminHome():Response
    {
       return $this->render('admin/home.html.twig');
    }

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

}