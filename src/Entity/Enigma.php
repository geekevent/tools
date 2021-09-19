<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;

#[Table(name: 'enigma')]
#[Entity]
class Enigma
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;

    #[Column(type: 'string', length: 50, nullable: false)]
    private string $name;

    #[Column(type: 'text', length: 1000, nullable: false)]
    private string $question;

    #[OneToMany(mappedBy: 'enigma', targetEntity: Answer::class)]
    #[OrderBy(['position' => 'ASC'])]
    private Collection $answers;

    #[Column(type: 'text', nullable: true)]
    private ?string $details = null;

    #[ManyToOne(targetEntity: Event::class, inversedBy: 'enigmas')]
    private Event $event;

    #[Column(type: 'string', nullable: true)]
    private ?string $code = null;

    #[Column(type: 'string', nullable: false)]
    private string $answer;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

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