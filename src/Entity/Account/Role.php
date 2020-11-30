<?php

declare(strict_types=1);

namespace App\Entity\Account;

use App\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity
 */
class Role extends AbstractEntity
{
    public const ROLES = [
        'ROLE_ADMIN' => 'Role admin',
    ];
    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true, options={"default": "empty"})
     */
    private string $identifier;

    /**
     * @var Collection<int, Module>
     * @ORM\ManyToMany(targetEntity="App\Entity\Account\Module", inversedBy="roles")
     */
    private Collection $modules;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": 1})
     */
    private bool $deletable = true;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->name = '';
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

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        if ($this->modules->contains($module)) {
            $this->modules->removeElement($module);
        }

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function resetModules(): self
    {
        $this->modules = new ArrayCollection();

        return $this;
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

    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    public function setDeletable(bool $deletable): self
    {
        $this->deletable = $deletable;

        return $this;
    }
}
