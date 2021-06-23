<?php

declare(strict_types=1);

namespace App\Form\Type\Account;

use App\Entity\Account\Account;
use App\Form\Type\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PasswordResetType extends AbstractFormType
{
    public function __construct()
    {
        $this->className = Account::class;
        $this->tokenId = 'account_reset_token';
    }

    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param array<mixed>                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'help' => 'le mot de passe doit contenir 8 caractère avec 1 majuscule, 1 chiffre et un caractère spécial',
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Create'])
        ;
    }
}
