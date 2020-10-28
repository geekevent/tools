<?php

namespace App\Entity\Account;

use App\Entity\AbstractEntity;
use App\Entity\IdentifiedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="module")
 */
class Module extends AbstractEntity implements IdentifiedEntity
{
    public const MODULE = [
        'ROLE' => 'Rôle',
        'MODULE' => 'Module',
    ];

    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private string $name;

    /**
     * @var Collection<int, Role>
     * @ORM\ManyToMany(targetEntity="App\Entity\Account\Role", inversedBy="modules")
     */
    private Collection $roles;

    /**
     * @ORM\Column(type="string", length=10, nullable=false, unique=true)
     */
    private string $identifier;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }
}
