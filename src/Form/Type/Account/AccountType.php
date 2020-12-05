<?php

declare(strict_types=1);

namespace App\Form\Type\Account;

use App\Entity\Account\Account;
use App\Entity\Account\Role;
use App\Form\Type\AbstractFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AccountType extends AbstractFormType
{
    protected string $buttonLabel;

    public function __construct()
    {
        $this->className = Account::class;
        $this->tokenId = 'account_creation_token';
        $this->buttonLabel = 'Ajouter';
    }

    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param array<mixed>                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'familyName',
                TextType::class,
                [
                    'label' => 'Nom de famille',
                    'help' => 'Nom de l\'utilisateur',
                    'attr' => [
                        'length' => 40,
                    ],
                ]
            )
            ->add(
                'givenName',
                TextType::class,
                [
                    'label' => 'Prénom',
                    'help' => 'Prénom de l\'utilisateur',
                    'attr' => [
                        'length' => 40,
                    ],
                ]
            )
            ->add(
                'login',
                TextType::class,
                [
                    'label' => 'login',
                    'help' => 'Indiquez le login de l\'utilisateur',
                    'attr' => [
                        'length' => 40,
                    ],
                ]
            )
            ->add(
                'role',
                EntityType::class,
                [
                    'label' => false,
                    'class' => Role::class,
                    'choice_label' => 'name',
                    'help' => 'Role de l\'utilisateur',
                ]
            )
            ->add('save', SubmitType::class, ['label' => $this->buttonLabel])
        ;
    }
}
