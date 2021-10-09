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
    public int $id;

    #[Column(type: 'string', length: 50, nullable: false)]
    public string $name;

    #[Column(type: 'text', length: 1000, nullable: false)]
    public string $question;

    #[OneToMany(mappedBy: 'enigma', targetEntity: Answer::class)]
    #[OrderBy(['position' => 'ASC'])]
    public Collection $answers;

    #[Column(type: 'text', nullable: true)]
    public ?string $details = null;

    #[ManyToOne(targetEntity: Event::class, inversedBy: 'enigmas')]
    public Event $event;

    #[Column(type: 'string', nullable: true)]
    public ?string $code = null;

    #[Column(type: 'string', nullable: false)]
    public string $answer;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }
}
