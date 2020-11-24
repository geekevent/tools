<?php

declare(strict_types=1);

namespace App\Entity\Account;

use App\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="account")
 * @ORM\Entity
 * @UniqueEntity("login")
 */
class Account extends AbstractEntity implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $password = null;

    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     * @Assert\Email
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
    private ?string $resetToken;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private string $givenName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private string $familyName;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
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

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getGivenName(): string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName): self
    {
        $this->givenName = $givenName;

        return $this;
    }

    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getRoles()
    {
        return [$this->role->getIdentifier()];
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->login,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->id,
            $this->login,
            $this->password,
        ] = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (null !== $this->plainPassword
            && !preg_match('/^(?=.*[A-Z]+)(?=.*[0-9]+)(?=.*[%\-_@&^$€£!?;.=,+]+)[a-zA-Z0-9%\-_@&^$€£!?;.=,+]{8,}$/', $this->plainPassword)) {
            $context->buildViolation('Le mot de passe ne respecte pas les règles')
                ->atPath('plainPassword')
                ->addViolation()
            ;
        }
    }
}
