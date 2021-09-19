<?php

namespace App\Form\Answer;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Create extends AbstractType
{
    private const TOKEN = 'answer_create';

    private const CLASSNAME = Answer::class;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('answer', TextType::class, [
                'label' => 'Réponse :',
                'required' => true,
                'attr' => [
                    'rows' => 6,
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'md-form form-outline',
                ],
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position :',
                'required' => true,
                'attr' => [
                    'rows' => 6,
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'md-form form-outline',
                ],
            ])
            ->add('exact', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'multiple' => false,
                'choices' => [
                    'oui' => true,
                    'non' => false,
                ],
                'help' => 'Bonne réponse',
                'attr' => [
                    'rows' => 6,
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'row_attr' => [
                    'class' => 'md-form form-outline',
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