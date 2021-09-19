<?php

namespace App\Form\CovidAuthorization;

use App\Entity\CovidAuthorization;
use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Create extends AbstractType
{
    private const TOKEN = 'covid_create';

    private const CLASSNAME = CovidAuthorization::class;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', ChoiceType::class, [
                'label' => 'Bénévole',
                'required' => true,
            ])
            ->add(
                'startDate',
                TextType::class,
                [
                    'label' => 'Date de début',
                    'required' => true,
                    'help' => 'Saisir la date au format dd/mm/yyyy',
                ]
            )
            ->add(
                'endDate',
                TextType::class,
                [
                    'label' => 'Date de fin',
                    'required' => true,
                    'help' => 'Saisir la date au format dd/mm/yyyy',
                ]
            )
            ->add(
                'startTime',
                TextType::class,
                [
                    'label' => 'Heure de début',
                    'required' => true,
                    'help' => 'Saisir l\'heure au format hh:mm',
                ]
            )
            ->add(
                'endTime',
                TextType::class,
                [
                    'label' => 'Heure de fin',
                    'required' => true,
                    'help' => 'Saisir l\'heure au format hh:mm',
                ]
            )
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

        $builder
            ->get('endDate')
            ->addModelTransformer(
                new CallbackTransformer(
                    function (?\DateTimeInterface $endDate) {
                        if (null === $endDate) {
                            return  null;
                        }

                        return $endDate->format('d/m/Y');
                    },
                    function (?string $endDate) {
                        if (null === $endDate) {
                            return null;
                        }

                        $parts = explode('/', $endDate);

                        return new \DateTime(sprintf('%s-%s-%s', $parts[0], $parts[1], $parts[2]));
                    },
                )
            )
        ;

        $builder
            ->get('startDate')
            ->addModelTransformer(
                new CallbackTransformer(
                    function (?\DateTimeInterface $endDate) {
                        if (null === $endDate) {
                            return  null;
                        }

                        return $endDate->format('d/m/Y');
                    },
                    function (?string $endDate) {
                        if (null === $endDate) {
                            return null;
                        }

                        $parts = explode('/', $endDate);

                        return new \DateTime(sprintf('%s-%s-%s', $parts[0], $parts[1], $parts[2]));
                    },
                )
            )
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