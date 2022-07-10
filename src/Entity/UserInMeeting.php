<?php

namespace App\Entity;

use App\Repository\UserInMeetingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInMeetingRepository::class)
 */
class UserInMeeting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isGoing;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userInMeetings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Meeting::class, inversedBy="userInMeetings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meeting;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsGoing(): ?bool
    {
        return $this->isGoing;
    }

    public function setIsGoing(bool $isGoing): self
    {
        $this->isGoing = $isGoing;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMeeting(): ?Meeting
    {
        return $this->meeting;
    }

    public function setMeeting(?Meeting $meeting): self
    {
        $this->meeting = $meeting;

        return $this;
    }
}
