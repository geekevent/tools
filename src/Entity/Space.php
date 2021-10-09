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
    public ?int $id = null;

    #[Column(type: 'string', length: 50, nullable: false)]
    public string $name;

    #[ManyToOne(targetEntity: Event::class, inversedBy: 'spaces')]
    public Event $event;

    #[Column(type: 'integer', nullable: false)]
    public int $gaugeMax;

    public int $gauge;

    public function isActive()
    {
        return $this->event->startDate <= new \DateTime() && $this->event->endDate >= new \DateTime();
    }
}
