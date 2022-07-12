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
    private $isGoing = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userInMeetings", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Meeting::class, inversedBy="userInMeetings", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meeting;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $declined;

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

    public function isDeclined(): ?bool
    {
        return $this->declined;
    }

    public function setDeclined(?bool $declined): self
    {
        $this->declined = $declined;

        return $this;
    }
}
