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
    private int $id;

    #[Column(type: 'string', length: 50, unique: true, nullable: false)]
    private string $name;

    #[Column(type: 'date', nullable: false)]
    private \DateTime $startDate;

    #[Column(type: 'date', nullable: false)]
    private \DateTime $endDate;

    #[OneToMany(mappedBy: 'event', targetEntity: Space::class, orphanRemoval: true)]
    private Collection $spaces;

    #[OneToMany(mappedBy: 'event', targetEntity: Enigma::class)]
    private Collection $enigmas;

    public function __get(string $name)
    {
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException('unkown '.$name.' attribute in '.User::class);
        }

        return $this->{$name};
    }

    public function __set(string $name, $value): void
    {
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException('unkown '.$name.' attribute in '.User::class);
        }

        $this->{$name} = $value;
    }

    public function __isset($name): bool
    {
        return property_exists($this, $name);
    }

    public function __unset(string $name): void
    {
        unset($this->{$name});
    }

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
