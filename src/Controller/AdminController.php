<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Sector;
use App\Entity\User;
use App\Entity\UserInMeeting;
use App\Form\ProfileFormType;
use App\Form\MeetingFormType;
use App\Form\RoomFormType;
use App\Form\SectorFormType;
use App\Form\UserFormType;
use App\Repository\MeetingRepository;
use App\Repository\RoomRepository;
use App\Repository\SectorRepository;
use App\Repository\UserInMeetingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /*rooms*/
    /**
     * @Route("admin/rooms/all", name="admin_showall_room")
     */
    public function showAllRoom(RoomRepository $rep): Response
    {
        $rooms = $rep->findAll();
        if (!$rooms) {
            throw $this->createNotFoundException('No rooms found!');
        }

        return $this->render('admin/room/all.html.twig', [
            'rooms' => $rooms
        ]);
    }

    /**
     * @Route("/admin/rooms/schedule", name="admin_room_schedule")
     */
    public function roomSchedule(): Response
    {
        return $this->render('admin/room/schedule.html.twig');
    }

    /**
     * @Route("/get_schedule", name="app_get_schedule", options={ "expose" = true})
     */
    public function getSchedule(MeetingRepository $meetingRep, Request $request)
    {
        $meetings = $meetingRep->findMeetingsByDate(
            $request->request->get('date')
        );
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($meetings,'json');

        return new JsonResponse($json);
    }

    /**
     * @Route("/admin/rooms/insert", name="admin_insert_room")
     * @IsGranted("ROLE_ADMIN")
     */
    public function insertRoom(EntityManagerInterface $em, Request $request): Response
    {

        $form = $this->createForm(RoomFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Room $room */
            $room = $form->getData();

            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'Nova sala je kreirana!');

            $this->redirectToRoute('admin_showall_room');

        }

        return $this->render('admin/room/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/rooms/edit/{id}", name="admin_edit_room")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editRoom(EntityManagerInterface $em, Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'Upesno ste izmenili podatke o sali!');
            return $this->redirectToRoute('admin_showall_room');

        }

        return $this->render('admin/room/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/rooms/delete/{id}", name="admin_delete_room")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteRoom(EntityManagerInterface $em, Room $room): Response
    {
        $em->remove($room);
        $em->flush();

        return $this->redirectToRoute('admin_showall_room');
    }

    /*users*/
    /**
     * @Route("/admin/users/all", name="admin_showall_users")
     */
    public function showAllUsers(UserRepository $repository): Response
    {
        $users = $repository->findAll();
        if (!$users) {
            throw $this->createNotFoundException('No users found!');
        }

        return $this->render('admin/user/all.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/users/edit/{id}", name="admin_edit_user")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editUser(EntityManagerInterface $em, Request $request, User $user): Response
    {
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Uspesno ste izmenili podatke o korisniku');
            return $this->redirectToRoute('admin_showall_users');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/users/delete/{id}", name="admin_delete_user")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteUser(EntityManagerInterface $em, User $user): Response
    {
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_showall_users');
    }

    /**
     * @Route ("/admin/users/make_admin/{id}", name="admin_make_user_admin")
     */
    public function makeUserAdmin(EntityManagerInterface $em, User $user): Response
    {
        $user->setRoles(['ROLE_ADMIN']);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_showall_users');
    }

    /**
     * @Route("/admin/users/make_chief/{id}", name="admin_make_user_chief")
     * @IsGranted("ROLE_ADMIN")
     */
    public function makeUserChief(EntityManagerInterface $em, User $user, UserRepository $userRepository): Response
    {

        $existingChief = $userRepository->findOneChiefOfSector($user->getSector()->getId());
        if($existingChief){
            $existingChief[0]->setRoles(['ROLE_USER']);
            $em->persist($existingChief[0]);
        }

        $user->setRoles(['ROLE_CHIEF']);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_showall_users');
    }

    /**
     * @Route ("/admin/users/make_regular/{id}", name="admin_make_user_regular")
     */
    public function makeUserRegular(EntityManagerInterface $em, User $user): Response
    {
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_showall_users');
    }

    /*sectors*/
    /**
     * @Route("/admin/sector/all", name="admin_showall_sectors")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showAllSectors(SectorRepository $repository): Response
    {
        $sectors = $repository->findAll();
        if (!$sectors) {
            throw $this->createNotFoundException('No sectors found!');
        }

        return $this->render('admin/sector/all.html.twig', [
            'sectors' => $sectors
        ]);
    }

    /**
     * @Route("/admin/sector/edit/{id}", name="admin_edit_sector")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editSector(EntityManagerInterface $em, Request $request, Sector $sector): Response
    {
        $form = $this->createForm(SectorFormType::class, $sector);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($sector);
            $em->flush();

            $this->addFlash('success', 'Uspesno ste izmenili podatke o sektoru');
            return $this->redirectToRoute('admin_showall_sectors');
        }

        return $this->render('admin/sector/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/sector/insert", name="admin_insert_sector")
     * @IsGranted("ROLE_ADMIN")
     */
    public function insertSector(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(SectorFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Sector $sector */
            $sector = $form->getData();

            $em->persist($sector);
            $em->flush();

            $this->addFlash('success', 'Novi sektor je kreiran');
            return $this->redirectToRoute('admin_showall_sectors');
        }

        return $this->render('admin/sector/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/sector/delete/{id}", name="admin_delete_sector")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteSector(EntityManagerInterface $em, Sector $sector): Response
    {
        $em->remove($sector);
        $em->flush();

        return $this->redirectToRoute('admin_showall_sectors');
    }

    /*meetings*/

    /**
     * @Route("/admin/meeting/all", name="admin_showall_meetings")
     */
    public function showAllMeetings(MeetingRepository $repository): Response
    {
        $meetings = $repository->findAll();
        if (!$meetings) {
            throw $this->createNotFoundException('No meetings found!');
        }

        return $this->render('admin/meeting/all.html.twig', [
            'meetings' => $meetings
        ]);
    }

    /**
     * @Route("/admin/meeting/edit/{id}", name="admin_edit_meeting")
     */
    public function editMeeting(EntityManagerInterface $em, Request $request, UserRepository $userRepository,MeetingRepository $meetingRep, UserInMeetingRepository $userInMeetingRepository, int $id): Response
    {

        $meetingFromDB = $meetingRep->findOneBy(['id' => $id]);
        $meetingFromDBUsers = $userRepository->findUsersOnMeeting($id);

        $form = $this->createForm(MeetingFormType::class, $meetingFromDB, [
            'room_field' => true,
            'selected_room' => $meetingFromDB->getRoom(),
            'meeting_id' => $meetingFromDB->getId(),
            'date_set' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newMeeting = $form->getData();
            $newMeetingUsers = $form->get('users')->getData();

            //da li je soba zauzeta
            if (($meetingFromDB->getRoom() !== $newMeeting->getRoom()) || ($meetingFromDB->getStart() !== $newMeeting->getStart()) || ($meetingFromDB->getEnd() !== $newMeeting->getEnd())) {
                $isRoomTaken = $meetingRep->findByIsRoomTakenForAnotherMeeting(
                    $newMeeting->getStart(),
                    $newMeeting->getEnd(),
                    $newMeeting->getRoom()->getId(),
                );

                if ($isRoomTaken) {
                    return $this->render('admin/meeting/edit.html.twig', [
                        'form' => $form->createView(),
                        'error_msg' => 'Soba je zauzeta u odabrano vreme! Sastanak nije sacuvan.',
                    ]);
                }
            }
            //provera da li ima vise oznacenih osoba nego sto je kapacitet
            if (count($newMeetingUsers) > $newMeeting->getRoom()->getSeatNumber()) {
                return $this->render('admin/meeting/edit.html.twig', [
                    'form' => $form->createView(),
                    'error_msg' => 'Odabrali ste vise osoba nego sto je kapacitet sobe! Sastanak nije sacuvan.',
                ]);
            }

            //provera da li ima manje oznacenih osoba nego sto je kapacitet
            if (count($newMeetingUsers) < $newMeeting->getRoom()->getSeatNumber()) {
                return $this->render('admin/meeting/edit.html.twig', [
                    'form' => $form->createView(),
                    'error_msg' => 'Odabrali ste manje osoba nego sto je kapacitet sobe! Sastanak nije sacuvan.',
                ]);
            }

            //provera svake osobe da li je zauzeta na drugom sastanku
            $errorMsg = "Osobe: ";
            foreach ($newMeetingUsers as $user){
                $isPersonBusy = $meetingRep->findByIsUserOnAnotherMeeting(
                    $newMeeting->getStart(),
                    $newMeeting->getEnd(),
                    $user->getId(),
                    $newMeeting->getId()
                );
                if ($isPersonBusy) {
                    $errorMsg .= $user->getFullName() . ", ";
                }else{
                    if(!in_array($user, $meetingFromDBUsers, true)){
                        //ako je dodata osoba koja pre nije bila na sastanku
                        $userInMeeting = new UserInMeeting();
                        $userInMeeting->setUser($user);
                        $userInMeeting->setMeeting($newMeeting);
                        $em->persist($userInMeeting);
                    }
                    $usersToDeleteFromMeeting = array_diff($meetingFromDBUsers,$newMeetingUsers);
                    //brisanje usera koji su izbaceni sa sastanka
                    foreach ($usersToDeleteFromMeeting as $us){
                        $u = $userInMeetingRepository->findOneBy(['user' => $us->getId(), 'meeting' => $newMeeting->getId()]);
                        $em->remove($u);
                    }
                }
            }
            if ($errorMsg !== "Osobe: ") {
                return $this->render('admin/meeting/edit.html.twig', [
                    'form' => $form->createView(),
                    'error_msg' => $errorMsg . ' su u to vreme na drugom sastanku. Sastanak nije sacuvan.',
                ]);
            }

            $em->persist($newMeeting);
            $em->flush();

            $this->addFlash('success', 'Uspesno ste izmenili sastanak!');
            return $this->redirectToRoute('admin_showall_meetings');
        }

        return $this->render('admin/meeting/edit.html.twig', [
            'form' => $form->createView(),
            'error_msg' => null
        ]);
    }

    /**
     * @Route("/admin/meeting/insert", name="admin_insert_meeting")
     */
    public function insertMeeting(Request $request, EntityManagerInterface $em, MeetingRepository $meetingRep): Response
    {
        $form = $this->createForm(MeetingFormType::class, null, [
            'room_field' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $meeting = $form->getData();
            $meeting->setCreator($this->getUser());
            $room = $form->get('room')->getData();

            $isRoomTaken = $meetingRep->findByIsRoomTakenForAnotherMeeting(
                $form->get('start')->getData(),
                $form->get('end')->getData(),
                $room->getId(),
            );

            //provera da li je soba zauzeta u dato vreme
            if ($isRoomTaken) {
                return $this->render('admin/meeting/new.html.twig', [
                    'form' => $form->createView(),
                    'error_msg' => 'Soba je zauzeta u odabrano vreme! Sastanak nije sacuvan.',
                ]);
            }

            //provera da li ima vise oznacenih osoba nego sto je kapacitet
            $userForMeeting = $form->get('users')->getData();
            if (count($userForMeeting) > $room->getSeatNumber()) {
                return $this->render('admin/meeting/new.html.twig', [
                    'form' => $form->createView(),
                    'error_msg' => 'Odabrali ste vise osoba nego sto je kapacitet sobe! Sastanak nije sacuvan.',
                ]);
            }

            //provera da li ima manje oznacenih osoba nego sto je kapacitet
            if (count($userForMeeting) < $room->getSeatNumber()) {
                return $this->render('admin/meeting/new.html.twig', [
                    'form' => $form->createView(),
                    'error_msg' => 'Odabrali ste manje osoba nego sto je kapacitet sobe! Sastanak nije sacuvan.',
                ]);
            }

            //dodavanje svakoga u sastanak sa defaultom isGoing = 0
            $errorMsg = "Osobe: ";
            foreach ($userForMeeting as $user) {
                //provera da li je osoba na drugom sastanku u dato vreme
               $isPersonBusy = $meetingRep->findByIsUserOnAnotherMeeting(
                    $form->get('start')->getData(),
                    $form->get('end')->getData(),
                    $user->getId(),
                    0
                );
                if ($isPersonBusy) {
                    $errorMsg .= $user->getFullName() . ", ";
                } else {
                    $userInMeeting = new UserInMeeting();
                    $userInMeeting->setUser($user);
                    $userInMeeting->setMeeting($meeting);
                    $em->persist($userInMeeting);
                }
            }
            if ($errorMsg !== "Osobe: ") {
                return $this->render('admin/meeting/new.html.twig', [
                    'form' => $form->createView(),
                    'error_msg' => $errorMsg . ' su u to vreme na drugom sastanku. Sastanak nije sacuvan.',
                ]);
            }

            $em->persist($meeting);
            $em->flush();

            $this->addFlash('success', 'Uspesno ste kreirali novi sastanak!');
            return $this->redirectToRoute('admin_showall_meetings');

        }

        return $this->render('admin/meeting/new.html.twig', [
            'form' => $form->createView(),
            'error_msg' => null
        ]);
    }

    /**
     * @Route("/admin/meeting/delete/{id}", name="admin_delete_meeting")
     */
    public function deleteMeeting(EntityManagerInterface $em, MeetingRepository $meetingRepository, UserInMeetingRepository $userInMeetingRepository, int $id): Response
    {

        $usersForMeeting = $userInMeetingRepository->findBy(['meeting' => $id]);
        foreach ($usersForMeeting as $u) {
            $em->remove($u);
        }
        $em->remove($meetingRepository->findOneBy(['id' => $id]));
        $em->flush();

        $this->addFlash('success', 'Sastanak je uspesno otkazan.');

        return $this->redirectToRoute('admin_showall_meetings');
    }

}