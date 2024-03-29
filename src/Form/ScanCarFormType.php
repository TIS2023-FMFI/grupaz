<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ScanCarFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vis', TextType::class, [
                'label' => 'entity.car.vis',
                'constraints' => [
                    new NotBlank(),
                    new Length(8)
                    ]
                ,
                'attr' => [
                    'autofocus' => 'autofocus',
                    'inputmode' => 'none',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => ['class' => 'btn btn-success'],
            ])
        ;
    }
}
