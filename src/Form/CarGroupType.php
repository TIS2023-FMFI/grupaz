<?php

namespace App\Form;

use App\Entity\CarGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('frontLicensePlate', null, [
                'label' => 'entity.carGroup.front_license_plate',
            ])
            ->add('backLicensePlate', null, [
                'label' => 'entity.carGroup.back_license_plate',
                //'help' => 'form_help.car.front_license_plate'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
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
