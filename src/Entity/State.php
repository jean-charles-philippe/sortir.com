<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=StateRepository::class)
 */
class State
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank
     * @Assert\Length(max=30)
     */
    private $tag;

    /**
     * @ORM\OneToMany(targetEntity=Vacation::class, mappedBy="state")
     */
    private $vacation;

    public function __construct()
    {
        $this->vacation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return Collection|Vacation[]
     */
    public function getVacation(): Collection
    {
        return $this->vacation;
    }

    public function addVacation(Vacation $vacation): self
    {
        if (!$this->vacation->contains($vacation)) {
            $this->vacation[] = $vacation;
            $vacation->setState($this);
        }

        return $this;
    }

    public function removeVacation(Vacation $vacation): self
    {
        if ($this->vacation->removeElement($vacation)) {
            // set the owning side to null (unless already changed)
            if ($vacation->getState() === $this) {
                $vacation->setState(null);
            }
        }

        return $this;
    }
}
