<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Me extends AbstractType
{
    private const TOKEN = 'user_me';

    private const CLASSNAME = User::class;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('givenName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('familyName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'help' => 'le mot de passe doit contenir 8 caractère avec 1 majuscule, 1 chiffre et un caractère spécial',
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
                'row_attr' => [
                    'class' => 'text-center md-form',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => self::CLASSNAME,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => self::TOKEN,
        ]);
    }
}