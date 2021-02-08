<?php


namespace App\Entity;


use phpDocumentor\Reflection\Types\Integer;

class PropertySearch
{

    private User $organiser;

    private ?string $campus;


    /**
     * @var
     */
    private ?string $host;

    /**
     * @var
     */
    private ?string $booked;

    /**
     * @var
     */
    private ?string $notBooked;

    /**
     * @var
     */
    private ?string $finished;

    /**
     * @var
     */
    private ?string $dateMin;

    /**
     * @var
     */
    private ?string  $datemax;

    /**
     * @var string
     */
    private ?string $word;

    /**
     * @return mixed
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getBooked(): ?string
    {
        return $this->booked;
    }

    /**
     * @param mixed $booked
     */
    public function setBooked(?string $booked): void
    {
        $this->booked = $booked;
    }

    /**
     * @return mixed
     */
    public function getNotBooked(): ?string
    {
        return $this->notBooked;
    }

    /**
     * @param mixed $notBooked
     */
    public function setNotBooked(?string $notBooked): void
    {
        $this->notBooked = $notBooked;
    }

    /**
     * @return mixed
     */
    public function getFinished(): ?string
    {
        return $this->finished;
    }

    /**
     * @param mixed $finished
     */
    public function setFinished(?string $finished): void
    {
        $this->finished = $finished;
    }

    /**
     * @return mixed
     */
    public function getDateMin(): ?string
    {
        return $this->dateMin;
    }

    /**
     * @param mixed $dateMin
     */
    public function setDateMin(?string $dateMin): void
    {
        $this->dateMin = $dateMin;
    }

    /**
     * @return mixed
     */
    public function getDatemax(): ?string
    {
        return $this->datemax;
    }

    /**
     * @param mixed $datemax
     */
    public function setDatemax(?string $datemax): void
    {
        $this->datemax = $datemax;
    }



    /**
     * @return string
     */
    public function getWord(): ?string
    {
        return $this->word;
    }

    /**
     * @param string $word
     */
    public function setWord(?string $word): void
    {
        $this->word = $word;
    }

    /**
     * @return User
     */
    public function getOrganiser(): User
    {
        return $this->organiser;
    }

    /**
     * @param User $organiser
     */
    public function setOrganiser(User $organiser): void
    {
        $this->organiser = $organiser;
    }

    /**
     * @return int
     */
    public function getCampus(): ?String
    {
        return $this->campus;
    }

    /**
     * @param int $campus
     */
    public function setCampus(String $campus): void
    {
        $this->campus = $campus;
    }


}