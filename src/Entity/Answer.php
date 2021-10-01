<?php


namespace App\Entity;


use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use phpDocumentor\Reflection\Types\String_;

#[Table(name: 'answer')]
#[Entity]
class Answer
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 100, nullable: false)]
    private string $answer;

    #[Column(type: 'integer', nullable: false)]
    private int $position;

    #[Column(type: 'boolean', nullable: false)]
    private bool $exact = false;

    #[ManyToOne(targetEntity: Enigma::class, inversedBy: 'answers')]
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