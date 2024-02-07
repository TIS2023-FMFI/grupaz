<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EndFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('submit', SubmitType::class, [
                'label' => '<i class="fa fa-home"></i>',
                'label_html' => true,
                'attr' => ['class' => 'btn-lg mt-3 px-5 fw-bold btn btn-danger'],
            ])
        ;
    }
}