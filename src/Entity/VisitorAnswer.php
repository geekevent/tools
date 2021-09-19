<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Table(name: 'visitor_answer')]
#[Entity]
class VisitorAnswer
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 50, nullable: false)]
    private string $email;

    #[Column(type: 'string', length: 10, unique: true, nullable: false)]
    private string $code;

    #[ManyToOne(targetEntity: Enigma::class)]
    private Enigma $enigma;

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