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
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $role;



    /**
     * @ORM\OneToMany(targetEntity=Meeting::class, mappedBy="creator")
     */
    private $meetings;

    /**
     * @ORM\ManyToMany(targetEntity=UserInMeeting::class, mappedBy="user")
     */
    private $userInMeetings;

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
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

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

    public function getRoles(): array
    {
        return array('ROLE_USER');
    }

    public function setRoles(array $roles): self
    {
        $this->role = $roles;
        return $this;
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


    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload){

        if($this->getFirstName() === null){
            $context->buildViolation("Unesite ime!")
                ->atPath('first_name')
                ->addViolation();
        }

        if($this->getLastName() === null){
            $context->buildViolation("Unesite prezime!")
                ->atPath('last_name')
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
            $context->buildViolation("Lozinka mora imati barem 8 karaktera!")
                ->atPath('password')
                ->addViolation();
        }

        if($this->getSector() === null){
            $context->buildViolation("Izaberite sektor!")
                ->atPath('sector')
                ->addViolation();
        }

    }


}
