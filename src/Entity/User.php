<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
     *     max = 50,
     *     minMessage="Ime moze imati najmanje 2 karaktera.",
     *     maxMessage="Ime moze imati najvise 50 karaktera."
     * )
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage="Prezime moze imati najmanje 2 karaktera.",
     *     maxMessage="Prezime moze imati najvise 50 karaktera."
     * )
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Email(
     *     message = "Nepravilan format email-a. (Primer: imeprezime@mail.com )"
     * )
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min = 8,
     *     max = 255,
     *     minMessage="Lozinka moze imati najmanje 8 karaktera.",
     *     maxMessage="Lozinka moze imati najvise 255 karaktera."
     * )
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles=[];



    /**
     * @ORM\OneToMany(targetEntity=Meeting::class, mappedBy="creator")
     */
    private Collection $meetings;

    /**
     * @ORM\ManyToMany(targetEntity=UserInMeeting::class, mappedBy="user")
     */
    private Collection $userInMeetings;

    /**
     * @ORM\ManyToOne(targetEntity=Sector::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sector;

    public function __construct()
    {
        $this->meetings = new ArrayCollection();
        $this->userInMeetings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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
            $meeting->setCreator($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): self
    {
        if ($this->meetings->removeElement($meeting)) {
            // set the owning side to null (unless already changed)
            if ($meeting->getCreator() === $this) {
                $meeting->setCreator(null);
            }
        }

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
            $userInMeeting->addUser($this);
        }

        return $this;
    }

    public function removeUserInMeeting(UserInMeeting $userInMeeting): self
    {
        if ($this->userInMeetings->removeElement($userInMeeting)) {
            $userInMeeting->removeUser($this);
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }



    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    //public function getPassword(): ?string
    //{
    //   return null;
    //}

    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getFullName():string
    {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    public function getAvatar(int $size = 32):string
    {
        return 'https://ui-avatars.com/api/?' . http_build_query([
                'name'=>$this->getFirstName(),
                'size'=>$size,
                'background'=>'random',
            ]);
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload){
/*
        if($this->getFirstName() === null){
            $context->buildViolation("Unesite ime!")
                ->atPath('firstName')
                ->addViolation();
        }

        if($this->getLastName() === null){
            $context->buildViolation("Unesite prezime!")
                ->atPath('lastName')
                ->addViolation();
        }

        if ($this->getEmail() === null){
            $context->buildViolation("Unesite email!")
                ->atPath('email')
                ->addViolation();
        }elseif (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)){
            $context->buildViolation("Unesite validan format email-a!")
                ->atPath('email')
                ->addViolation();
        }

        if($this->getPassword() === null){
            $context->buildViolation("Unesite lozinku!")
                ->atPath('password')
                ->addViolation();
        }elseif(strlen($this->getPassword()) < 8){
            $context->buildViolation("Lozinka moze imati barem 8 karaktera!")
                ->atPath('password')
                ->addViolation();
        }

        if($this->getSector() === null){
            $context->buildViolation("Izaberite sektor!")
                ->atPath('sector')
                ->addViolation();
        }*/

    }


}
