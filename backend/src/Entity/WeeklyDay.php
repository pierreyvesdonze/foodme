<?php

namespace App\Entity;

use App\Repository\WeeklyDayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeeklyDayRepository::class)]
class WeeklyDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $day = null;

    #[ORM\Column(length: 255)]
    private ?string $breakfast = null;

    #[ORM\Column(length: 255)]
    private ?string $lunch = null;

    #[ORM\Column(length: 255)]
    private ?string $dinner = null;

    #[ORM\ManyToOne(inversedBy: 'weeklyDay', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?WeeklyMenu $weeklyMenu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getBreakfast(): ?string
    {
        return $this->breakfast;
    }

    public function setBreakfast(string $breakfast): self
    {
        $this->breakfast = $breakfast;

        return $this;
    }

    public function getLunch(): ?string
    {
        return $this->lunch;
    }

    public function setLunch(string $lunch): self
    {
        $this->lunch = $lunch;

        return $this;
    }

    public function getDinner(): ?string
    {
        return $this->dinner;
    }

    public function setDinner(string $dinner): self
    {
        $this->dinner = $dinner;

        return $this;
    }

    public function getWeeklyMenu(): ?WeeklyMenu
    {
        return $this->weeklyMenu;
    }

    public function setWeeklyMenu(?WeeklyMenu $weeklyMenu): self
    {
        $this->weeklyMenu = $weeklyMenu;

        return $this;
    }
}
