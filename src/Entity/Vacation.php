<?php

namespace App\Entity;

use App\Repository\VacationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     * @Assert\Length(max=50)
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
     * @Assert\Type(type="integer")
     */
    private $placeNumber;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="vacations")
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="vacations")
     */
    private $campus;


    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="vacation")
     */
    private $state;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $booked;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="vacations")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="vacationsOrganiser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organiser;



    public function __construct()
    {
        $this->participants = new ArrayCollection();
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


    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }



    public function getBooked(): ?int
    {
        return $this->booked;
    }

    public function setBooked(?int $booked): self
    {
        $this->booked = $booked;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getOrganiser(): ?User
    {
        return $this->organiser;
    }

    public function setOrganiser(?User $organiser): self
    {
        $this->organiser = $organiser;

        return $this;
    }





}
