<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionRepository::class)
 */
class Inscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=vacation::class, inversedBy="inscriptions")
     */
    private $vacation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inscriptions")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVacation(): ?vacation
    {
        return $this->vacation;
    }

    public function setVacation(?vacation $vacation): self
    {
        $this->vacation = $vacation;

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
}
