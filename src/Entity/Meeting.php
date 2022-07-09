<?php

namespace App\Entity;

use App\Repository\MeetingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MeetingRepository::class)
 */
class Meeting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="meetings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="meetings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min = 10,
     *     max = 255,
     *     minMessage="Opis sastanka moze imati najmanje 10 karaktera.",
     *     maxMessage="Opis sastanka moze imati najvise 255 karaktera."
     * )
     */
    private string $description;

    /**
     * @ORM\ManyToMany(targetEntity=UserInMeeting::class, mappedBy="meeting")
     */
    private $userInMeetings;

    public function __construct()
    {
        $this->userInMeetings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, UserInMeeting>
     */
    public function getUserInMeetings(): Collection
    {
        return $this->userInMeetings;
    }

    public function addUserInMeeting(UserInMeeting $userInMeeting): self
    {
        if (!$this->userInMeetings->contains($userInMeeting)) {
            $this->userInMeetings[] = $userInMeeting;
            $userInMeeting->addMeeting($this);
        }

        return $this;
    }

    public function removeUserInMeeting(UserInMeeting $userInMeeting): self
    {
        if ($this->userInMeetings->removeElement($userInMeeting)) {
            $userInMeeting->removeMeeting($this);
        }

        return $this;
    }
}
