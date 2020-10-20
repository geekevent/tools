<?php

namespace App\Entity\Account;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity
 */
class Role extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
