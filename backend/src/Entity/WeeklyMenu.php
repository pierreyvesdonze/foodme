<?php

namespace App\Entity;

use App\Repository\WeeklyMenuRepository;
use App\Entity\WeeklyDay;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeeklyMenuRepository::class)]
class WeeklyMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'weeklyMenu', targetEntity: WeeklyDay::class, orphanRemoval: true, cascade:['persist'])]
    private Collection $weeklyDay;

    public function __construct()
    {
        $this->weeklyDay = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, WeeklyDay>
     */
    public function getWeeklyDay(): Collection
    {
        return $this->weeklyDay;
    }

    public function addWeeklyDay(WeeklyDay $weeklyDay): self
    {
        if (!$this->weeklyDay->contains($weeklyDay)) {
            $this->weeklyDay->add($weeklyDay);
            $weeklyDay->setWeeklyMenu($this);
        }

        return $this;
    }

    public function removeWeeklyDay(WeeklyDay $weeklyDay): self
    {
        if ($this->weeklyDay->removeElement($weeklyDay)) {
            // set the owning side to null (unless already changed)
            if ($weeklyDay->getWeeklyMenu() === $this) {
                $weeklyDay->setWeeklyMenu(null);
            }
        }

        return $this;
    }
}
