<?php

namespace App\Entity\Account;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="account")
 * @ORM\Entity
 */
class Account extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    private string $login;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private \DateTime $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account\Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private Role $role;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private string $connectionToken;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private string $resetToken;

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getConnectionToken(): string
    {
        return $this->connectionToken;
    }

    public function setConnectionToken(string $connectionToken): self
    {
        $this->connectionToken = $connectionToken;

        return $this;
    }

    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    public function setResetToken(string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }
}
