<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Sector;
use App\Entity\User;
use App\Form\ProfileFormType;
use App\Form\RoomFormType;
use App\Form\SectorFormType;
use App\Repository\RoomRepository;
use App\Repository\SectorRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

    /*rooms*/
    /**
     * @Route("admin/rooms/all", name="admin_showall_room")
     */
    public function showAllRoom(RoomRepository $rep):Response
    {
        $rooms = $rep->findAll();
        if(!$rooms){
            throw $this->createNotFoundException('No rooms found!');
        }

        return $this->render('admin/room/all.html.twig', [
            'rooms' => $rooms
        ]);
    }

    /**
     * @Route("/admin/rooms/insert", name="admin_insert_room")
     */
    public function insertRoom(EntityManagerInterface $em, Request $request):Response
    {

        $form = $this->createForm(RoomFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var Room $room */
            $room = $form->getData();

            $em->persist($room);
            $em->flush();

            $this->addFlash('success','Nova sala je kreirana!');

            $this->redirectToRoute('admin_showall_room',[
               // 'success'=>'Nova sala je kreirana!',
            ]);

        }

        return $this->render('admin/room/new.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/rooms/edit/{id}", name="admin_edit_room")
     */
    public function editRoom(EntityManagerInterface $em, Request $request, Room $room):Response
    {
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($room);
            $em->flush();

            $this->addFlash('success','Upesno ste izmenili podatke o sali!');
            //zbog flasha ostaje na istoj stranici

        }

        return $this->render('admin/room/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/rooms/delete/{id}", name="admin_delete_room")
     */
    public function deleteRoom(EntityManagerInterface $em, Room $room):Response
    {
        $em->remove($room);
        $em->flush();

        return $this->redirectToRoute('admin_showall_room');
    }

    /*rooms*/
    /**
     * @Route("/admin/users/all", name="admin_showall_users")
     */
    public function showAllUsers(UserRepository $repository):Response
    {
        $users = $repository->findAll();
        if(!$users){
            throw $this->createNotFoundException('No users found!');
        }

        return $this->render('admin/user/all.html.twig',[
            'users'=>$users
        ]);
    }
    /**
     * @Route("/admin/users/edit/{id}", name="admin_edit_user")
     */
    public function editUser(EntityManagerInterface $em, Request $request, User $user):Response
    {
        $form = $this->createForm(ProfileFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Uspesno ste izmenili podatke o korisniku');

        }

        return $this->render('admin/user/edit.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/users/delete/{id}", name="admin_delete_user")
     */
    public function deleteUser(EntityManagerInterface $em, User $user):Response
    {
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_showall_users');
    }

    /*sectors*/
    /**
     * @Route("/admin/sector/all", name="admin_showall_sectors")
     */
    public function showAllSectors(SectorRepository $repository):Response
    {
        $sectors = $repository->findAll();
        if(!$sectors){
            throw $this->createNotFoundException('No sectors found!');
        }

        return $this->render('admin/sector/all.html.twig',[
            'sectors' => $sectors
        ]);
    }

    /**
     * @Route("/admin/sector/edit/{id}", name="admin_edit_sector")
     */
    public function editSector(EntityManagerInterface $em, Request $request, Sector $sector):Response
    {
        $form = $this->createForm(SectorFormType::class, $sector);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($sector);
            $em->flush();

            $this->addFlash('success', 'Uspesno ste izmenili podatke o sektoru');
        }

        return $this->render('admin/sector/edit.html.twig', [
            'form' =>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/sector/insert", name="admin_insert_sector")
     */
    public function insertSector(EntityManagerInterface $em, Request $request):Response
    {
        $form = $this->createForm(SectorFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var Sector $sector */
            $sector = $form->getData();

            $em->persist($sector);
            $em->flush();

            $this->addFlash('success', 'Novi sektor je kreiran');
        }

        return $this->render('admin/sector/new.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/sector/delete/{id}", name="admin_delete_sector")
     */
    public function deleteSector(EntityManagerInterface $em, Sector $sector):Response
    {
        $em->remove($sector);
        $em->flush();

        return $this->redirectToRoute('admin_showall_sectors');
    }


}