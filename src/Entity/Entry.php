<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Table(name: 'entry')]
#[Entity]
class Entry
{

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'integer', nullable: false)]
    private int $value;

    #[Column(type: 'datetime', nullable: false)]
    private \DateTime $time;

    #[ManyToOne(targetEntity: Space::class)]
    #[JoinColumn(name: 'space_id', referencedColumnName: 'id')]
    private Space $space;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[Column(type: 'string', length: 4, nullable: false)]
    private string $moment;

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
}
