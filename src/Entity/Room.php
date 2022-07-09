<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min = 2,
     *     max = 255,
     *     minMessage="Naziv sobe moze imati najmanje 2 karaktera.",
     *     maxMessage="Naziv sobe moze imati najvise 255 karaktera."
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     * @Assert\Range(
     *     min = 2,
     *     max = 500,
     *     notInRangeMessage = "Kapacitet treba biti u rasponu 2-500."
     * )
     */
    private int $seatNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min = 2,
     *     max = 255,
     *     minMessage="Naziv grada moze imati najmanje 2 karaktera.",
     *     maxMessage="Naziv grada moze imati najvise 255 karaktera."
     * )
     */
    private string $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min = 2,
     *     max = 255,
     *     minMessage="Naziv ulice moze imati najmanje 2 karaktera.",
     *     maxMessage="Naziv ulice moze imati najvise 255 karaktera."
     * )
     */
    private string $street;

    /**
     * @ORM\OneToMany(targetEntity=Meeting::class, mappedBy="room")
     */
    private $meetings;

    public function __construct()
    {
        $this->meetings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSeatNumber(): ?int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(int $seatNumber): self
    {
        $this->seatNumber = $seatNumber;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): self
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings[] = $meeting;
            $meeting->setRoom($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): self
    {
        if ($this->meetings->removeElement($meeting)) {
            // set the owning side to null (unless already changed)
            if ($meeting->getRoom() === $this) {
                $meeting->setRoom(null);
            }
        }

        return $this;
    }
}
