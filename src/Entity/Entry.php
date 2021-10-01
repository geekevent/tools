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
    public int $value;

    #[Column(type: 'datetime', nullable: false)]
    public \DateTime $time;

    #[ManyToOne(targetEntity: Space::class)]
    #[JoinColumn(name: 'space_id', referencedColumnName: 'id')]
    public Space $space;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    public User $user;

    #[Column(type: 'string', length: 4, nullable: false)]
    public string $moment;
}
