<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Table(name: 'covid_authorization')]
#[Entity]
class CovidAuthorization
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    public ?int $id = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'covidAuthorizations')]
    public User $user;

    #[Column(type: 'date', nullable: false)]
    public \DateTime $startDate;

    #[Column(type: 'date', nullable: false)]
    public \DateTime $endDate;

    #[Column(type: 'string', length: 5, nullable: false)]
    public string $startTime;

    #[Column(type: 'string', length: 5, nullable: false)]
    public string $endTime;
}
