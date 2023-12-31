<?php

namespace App\Form;

use App\Entity\CarGroup;
use App\Form\DataTransformer\CarGroupToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FilterCarGroupType extends AbstractType
{

    public function __construct(
        private CarGroupToStringTransformer $transformer,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('gid', null, [
                'label' => 'entity.carGroup.gid',
                'label_attr' => [
                    'class' => 'col-form-label-lg fw-bold'
                ],
                'attr' => [
                    'class' => 'form-control-lg'
                ],
                'constraints' => [new NotBlank(),],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit',
                'attr' => [
                    'class' => 'btn-lg btn btn-success px-5 fw-bold'
                ],
            ])
        ;

        $builder
            ->get('gid')
            ->addModelTransformer($this->transformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => CarGroup::class,
        ]);
    }
}
