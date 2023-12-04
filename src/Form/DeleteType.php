<?php

namespace App\Form;

use App\Validator\Constraints\DateRange;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', DateType::class, [
                'label' => 'delete.start',
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [new NotBlank(),],
                'attr' => [
                    'max' => (new DateTimeImmutable())->format('Y-m-d'),
                ],
            ])
            ->add('end', DateType::class, [
                'label' => 'delete.end',
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'max' => (new DateTimeImmutable())->format('Y-m-d'),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => [
                    'onsubmit' => 'return confirmDelete();',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [new DateRange()],
        ]);
    }
}