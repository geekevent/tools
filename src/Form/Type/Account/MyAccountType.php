<?php

declare(strict_types=1);

namespace App\Form\Type\Account;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class MyAccountType extends AccountType
{
    public function __construct()
    {
        parent::__construct();
        $this->tokenId = 'account_update_token';
        $this->buttonLabel = 'Modifier';
    }

    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param array<mixed>                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'required' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'help' => 'le mot de passe doit contenir 8 caractère avec 1 majuscule, 1 chiffre et un caractère spécial',
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                ],
            ])
        ;

        $builder->get('login')->setDisabled(true);
        $builder->get('role')->setDisabled(true);
        $builder->get('endDate')
            ->setRequired(false)
            ->setDisabled(true)
        ;
    }
}
