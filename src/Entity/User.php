<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 * @UniqueEntity("pseudo")
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Length(max=180)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=50)
     * @Assert\NotBlank
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max=50)
     * @Assert\NotBlank()
     */
    private ?string $firstName;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Type("string")
     * @Assert\Length(max=15)
     */
    private ?string $phone_number;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $active;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $admin;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private ?string $pseudo;


    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="users")
     */
    private $campus;

    /**
     * @ORM\ManyToMany(targetEntity=Vacation::class, mappedBy="participants")
     */
    private $vacations;

    /**
     * @ORM\OneToMany(targetEntity=Vacation::class, mappedBy="organiser")
     */
    private $vacationsOrganiser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pictureUserFileName;



    public function __construct()
    {
        $this->vacations = new ArrayCollection();
        $this->vacationsOrganiser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }
    

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function __toString()
{
    return $this->getName()." ".$this->getFirstName();
}

    /**
     * @return Collection|Vacation[]
     */
    public function getVacations(): Collection
    {
        return $this->vacations;
    }

    public function addVacation(Vacation $vacation): self
    {
        if (!$this->vacations->contains($vacation)) {
            $this->vacations[] = $vacation;
            $vacation->addParticipant($this);
        }

        return $this;
    }

    public function removeVacation(Vacation $vacation): self
    {
        if ($this->vacations->removeElement($vacation)) {
            $vacation->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Vacation[]
     */
    public function getVacationsOrganiser(): Collection
    {
        return $this->vacationsOrganiser;
    }

    public function addVacationsOrganiser(Vacation $vacationsOrganiser): self
    {
        if (!$this->vacationsOrganiser->contains($vacationsOrganiser)) {
            $this->vacationsOrganiser[] = $vacationsOrganiser;
            $vacationsOrganiser->setOrganiser($this);
        }

        return $this;
    }

    public function removeVacationsOrganiser(Vacation $vacationsOrganiser): self
    {
        if ($this->vacationsOrganiser->removeElement($vacationsOrganiser)) {
            // set the owning side to null (unless already changed)
            if ($vacationsOrganiser->getOrganiser() === $this) {
                $vacationsOrganiser->setOrganiser(null);
            }
        }

        return $this;
    }

    public function getPictureUserFileName(): ?string
    {
        return $this->pictureUserFileName;
    }

    public function setPictureUserFileName(?string $pictureUserFileName): self
    {
        $this->pictureUserFileName = $pictureUserFileName;

        return $this;
    }




}
