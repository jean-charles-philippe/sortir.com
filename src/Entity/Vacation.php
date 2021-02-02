<?php

namespace App\Entity;

use App\Repository\VacationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VacationRepository::class)
 */
class Vacation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $vacation_date;

    /**
     * @ORM\Column(type="date")
     */
    private $vacation_limitDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $placeNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="vacations")
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="vacations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="vacations")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="vacation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

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

    public function getVacationDate(): ?\DateTimeInterface
    {
        return $this->vacation_date;
    }

    public function setVacationDate(\DateTimeInterface $vacation_date): self
    {
        $this->vacation_date = $vacation_date;

        return $this;
    }

    public function getVacationLimitDate(): ?\DateTimeInterface
    {
        return $this->vacation_limitDate;
    }

    public function setVacationLimitDate(\DateTimeInterface $vacation_limitDate): self
    {
        $this->vacation_limitDate = $vacation_limitDate;

        return $this;
    }

    public function getPlaceNumber(): ?int
    {
        return $this->placeNumber;
    }

    public function setPlaceNumber(int $placeNumber): self
    {
        $this->placeNumber = $placeNumber;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

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

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }
}