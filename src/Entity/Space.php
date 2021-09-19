<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Table(name: 'space')]
#[UniqueConstraint(name: 'name', columns: ['name', 'event_id'])]
#[Entity]
#[UniqueEntity(['name', 'event'], 'Ce nom est déjà utilisé')]
class Space
{

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 50, nullable: false)]
    private string $name;

    #[ManyToOne(targetEntity: Event::class)]
    private Event $event;

    #[Column(type: 'integer', nullable: false)]
    private int $gaugeMax;

    private int $gauge;

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

    public function isActive(): bool
    {
        return $this->event->endDate >= new \DateTime() && $this->event->startDate <= new \DateTime();
    }
}
