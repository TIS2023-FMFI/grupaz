<?php

namespace App\Form;

use App\Entity\CarGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CarGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('frontLicensePlate', null, [
                'label' => 'entity.carGroup.front_license_plate',
                'constraints' => [new NotBlank(),],
                'label_attr' => [
                    'class' => 'col-form-label-lg fw-bold'
                ],
                'attr' => [
                    'class' => 'form-control-lg'
                ],
            ])
            ->add('backLicensePlate', null, [
                'label' => 'entity.carGroup.back_license_plate',
                'constraints' => [new NotBlank(),],
                'label_attr' => [
                    'class' => 'col-form-label-lg fw-bold'
                ],
                'attr' => [
                    'class' => 'form-control-lg'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => [
                    'class' => 'btn-lg btn btn-success px-5 fw-bold'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarGroup::class,
            'validation_groups' => ['worker_form',],
        ]);
    }
}
