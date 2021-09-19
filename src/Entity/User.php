<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

#[Table(name: 'user')]
#[Entity]
#[UniqueEntity('login', 'Cette valeur est déjà utilisé')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_ORGA = 'ROLE_ORGA';
    public const ROLE_CHEF_DE_POLE = 'ROLE_CHEF_DE_POLE';
    public const ROLE_STAFF = 'ROLE_STAFF';
    public const ROLE_VISITEUR = 'ROLE_VISITEUR';

    public const ROLES = [
        'Administrateur' => self::ROLE_ADMIN,
        'Organisateur' => self::ROLE_ORGA,
        'Chef de pole' => self::ROLE_CHEF_DE_POLE,
        'Staff' => self::ROLE_STAFF,
        'visiteur' => self::ROLE_VISITEUR,
    ];

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    public int $id;

    #[Column(type: 'string', length: 50, unique: true, nullable: false)]
    public string $login;

    #[Column(type: 'string', length: 50, nullable: true)]
    public ?string $resetToken = null;

    #[Column(type: 'string', length: 75, nullable: true)]
    public ?string $password = null;

    public ?string $plainPassword = null;

    #[Column(type: 'array', nullable: false)]
    public array $roles = [];

    #[Column(type: 'string', length: 50, nullable: false)]
    public string $givenName;

    #[Column(type: 'string', length: 50, nullable: false)]
    public string $familyName;

    #[OneToMany(mappedBy: 'user', targetEntity: CovidAuthorization::class)]
    public Collection $covidAuthorizations;

    public function __construct()
    {
        $this->covidAuthorizations = new ArrayCollection();
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function getUserIdentifier()
    {
        return $this->login;
    }
}
