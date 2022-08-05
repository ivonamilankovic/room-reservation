<?php

namespace App\Service;

use App\Entity\Meeting;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\MeetingRepository;
use App\Repository\RoomRepository;

class MeetingChecksHelper
{

    private $meetingRepository;

    public function __construct(MeetingRepository $meetingRep)
    {
        $this->meetingRepository = $meetingRep;
    }

    public function checkAvailability(Meeting $meeting, Room $room, User $creator, array $usersForMeeting):?string
    {
        $isCreatorBusy = $this->meetingRepository->findByIsUserOnAnotherMeeting(
            $meeting->getStart(),
            $meeting->getEnd(),
            $creator,
            0
        );
        if($isCreatorBusy){
            return 'Vi ste u to vreme na drugom sastanku. Sastanak nije sacuvan.';
        }

        $isRoomTaken = $this->meetingRepository->findByIsRoomTakenForAnotherMeeting(
            $meeting->getStart(),
            $meeting->getEnd(),
            $room->getId()
        );
        if($isRoomTaken){
            return 'Soba je zauzeta u odabrano vreme! Sastanak nije sacuvan.';
        }

        if(count($usersForMeeting)+1 > $room->getSeatNumber()){ //+1 jer se broji i kreator
            return 'Odabrali ste vise osoba nego sto je kapacitet sobe! Sastanak nije sacuvan.';
        }
        if(count($usersForMeeting)+1 < $room->getSeatNumber()){
            return 'Odabrali ste manje osoba nego sto je kapacitet sobe! Sastanak nije sacuvan.';
        }

        $errorMsg = "Osobe: ";
        foreach ($usersForMeeting as $user) {
            $isPersonBusy = $this->meetingRepository->findByIsUserOnAnotherMeeting(
                $meeting->getStart(),
                $meeting->getEnd(),
                $user->getId(),
                0
            );
            if ($isPersonBusy) {
                $errorMsg .= $user->getFullName() . ", ";
            }
        }

        if($errorMsg !== "Osobe: "){
            return $errorMsg.' su u to vreme na drugom sastanku. Sastanak nije sacuvan.';
        }

        return null;

    }


}