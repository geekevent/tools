<?php

namespace App\Form\Enigma;

use App\Entity\Enigma;
use App\Entity\Event;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Create extends AbstractType
{
    private const TOKEN = 'enigma_create';

    private const CLASSNAME = Enigma::class;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextareaType::class, [
                'label' => 'Enigme :',
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
            ->add('details', TextareaType::class, [
                'label' => 'Détails :',
                'required' => false,
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
            ->add('event', EntityType::class, [
                'label' => false,
                'placeholder' => 'événement',
                'class' => Event::class,
                'required' => true,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('ev')
                        ->where('ev.startDate > :now')
                        ->orderBy('ev.startDate', 'ASC')
                        ->setParameter('now', (new \DateTime())->format('Y-m-d'))
                        ;
                },
                'choice_label' => 'name'

            ])
            ->add('name', TextType::class, [
                'label' => 'nom',
                'required' => true,

            ])
            ->add('answer', TextType::class, [
                'label' => 'réponse',
                'required' => true,

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
