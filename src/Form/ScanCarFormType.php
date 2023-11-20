<?php

namespace App\Form;

use App\Entity\CarGroup;
use App\Form\DataTransformer\CarGroupToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ScanCarFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vis', null, [
                'label' => 'entity.car.vis',
                'constraints' => [new NotBlank(),],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
            ])
        ;
    }
}
