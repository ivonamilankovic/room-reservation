<?php

namespace App\Entity;

use App\Repository\UserInMeetingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private int $id;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="userInMeetings")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Meeting::class, inversedBy="userInMeetings")
     */
    private $meeting;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $is_going = false;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->meeting = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeeting(): Collection
    {
        return $this->meeting;
    }

    public function addMeeting(Meeting $meeting): self
    {
        if (!$this->meeting->contains($meeting)) {
            $this->meeting[] = $meeting;
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): self
    {
        $this->meeting->removeElement($meeting);

        return $this;
    }

    public function getIsGoing(): ?bool
    {
        return $this->is_going;
    }

    public function setIsGoing(bool $is_going): self
    {
        $this->is_going = $is_going;

        return $this;
    }
}
