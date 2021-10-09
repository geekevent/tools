<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Table(name: 'event')]
#[Entity]
#[UniqueEntity('name', 'Ce nom est déjà utilisé')]
class Event
{

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    public ?int $id = null;

    #[Column(type: 'string', length: 50, unique: true, nullable: false)]
    public string $name;

    #[Column(type: 'date', nullable: false)]
    public \DateTime $startDate;

    #[Column(type: 'date', nullable: false)]
    public \DateTime $endDate;

    #[OneToMany(mappedBy: 'event', targetEntity: Space::class, orphanRemoval: true)]
    public Collection $spaces;

    #[OneToMany(mappedBy: 'event', targetEntity: Enigma::class)]
    public Collection $enigmas;

    public function addSpace(Space $space): void
    {
        if ($this->spaces->contains($space)) {
            return;
        }

        $this->spaces[] = $space;
    }

    public function removeSpace(Space $space): void
    {
        if (!$this->spaces->contains($space)) {
            return;
        }

        $this->spaces->removeElement($space);
    }
}
