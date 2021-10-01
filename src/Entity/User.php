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
    public const ROLE_STAFF_ACCUEIL = 'ROLE_STAFF_ACCUEIL';
    public const ROLE_STAFF_ANIMATION = 'ROLE_STAFF_ANIMATION';
    public const ROLE_STAFF_VIDEO_GAME = 'ROLE_STAFF_VIDEO_GAME';
    public const ROLE_STAFF_TECHNIQUE = 'ROLE_STAFF_TECHNIQUE';
    public const ROLE_STAFF_BUVETTE = 'ROLE_STAFF_BUVETTE';
    public const ROLE_STAFF_INVITE = 'ROLE_STAFF_INVITE';

    public const ROLES = [
        'Administrateur'   => self::ROLE_ADMIN,
        'Organisateur'     => self::ROLE_ORGA,
        'Chef de pole'     => self::ROLE_CHEF_DE_POLE,
        'Staff'            => self::ROLE_STAFF,
        'Staff accueil'    => self::ROLE_STAFF_ACCUEIL,
        'Staff animation'  => self::ROLE_STAFF_ANIMATION,
        'Staff jeux-vidéo' => self::ROLE_STAFF_VIDEO_GAME,
        'Staff Technique'  => self::ROLE_STAFF_TECHNIQUE,
        'Staff Buvette'    => self::ROLE_STAFF_BUVETTE,
        'Staff Invité'     => self::ROLE_STAFF_INVITE,
        'Visiteur'         => self::ROLE_VISITEUR,
    ];

    private const ROLES_STRING = [
        self::ROLE_ADMIN            => 'Administrateur',
        self::ROLE_ORGA             => 'Organisateur',
        self::ROLE_CHEF_DE_POLE     => 'Chef de pole',
        self::ROLE_STAFF            => 'Staff',
        self::ROLE_STAFF_ACCUEIL    => 'Staff accueil',
        self::ROLE_STAFF_ANIMATION  => 'Staff animation',
        self::ROLE_STAFF_VIDEO_GAME => 'Staff jeux-vidéo',
        self::ROLE_STAFF_TECHNIQUE  => 'Staff Technique',
        self::ROLE_STAFF_BUVETTE    => 'Staff Buvette',
        self::ROLE_STAFF_INVITE     => 'Staff Invité',
        self::ROLE_VISITEUR         => 'Visiteur',
    ];

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    public ?int $id;

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

    public function displayRoles(): string
    {
        $roles = [];

        foreach ($this->roles as $role) {
            $roles[] = self::ROLES_STRING[$role];
        }

        return implode(', ', $roles);
    }
}
