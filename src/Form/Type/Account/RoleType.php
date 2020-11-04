<?php

declare(strict_types=1);

namespace App\Form\Type\Account;

use App\Entity\Account\Role;
use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RoleType extends AbstractFormType
{
    public function __construct()
    {
        $this->className = Role::class;
        $this->tokenId = 'role_token';
    }

    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param array<mixed>                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'help' => 'Nom du rôle',
                    'attr' => [
                        'length' => 10,
                    ],
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Create'])
        ;
    }
}
