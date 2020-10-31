<?php

declare(strict_types=1);

namespace App\Form\Type\Account;

use App\Entity\Account\Module;
use App\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ModuleType extends AbstractFormType
{
    public function __construct()
    {
        $this->className = Module::class;
        $this->tokenId = 'module_token';
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
                    'help' => 'Indiquez le nom du module',
                    'attr' => [
                        'length' => 10,
                    ],
                ]
            )
            ->add(
                'identifier',
                TextType::class,
                [
                    'help' => 'Identifiant du module',
                    'attr' => [
                        'length' => 10,
                        'disabled' => true,
                    ],
                    'mapped' => false,
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Create'])
        ;
    }
}
