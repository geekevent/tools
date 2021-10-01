<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Create extends AbstractType
{
    private const TOKEN = 'user_create';

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
            ->add('login', EmailType::class, [
                'label' => 'Addrese mail',
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => User::ROLES,
                'label' => false,
                'required' => true,
                'multiple' => true,
                'help' => 'Role de l\'utilisateur',
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

//        $builder->get('roles')
//            ->addModelTransformer(new CallbackTransformer(
//                function ($rolesArray) {
//                    return count($rolesArray)? $rolesArray[0]: null;
//                },
//                function ($rolesString) {
//                    return [$rolesString];
//                }
//            ));
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
