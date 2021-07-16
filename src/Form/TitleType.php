<?php

namespace App\Form;

use App\Entity\Title;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('round')
            ->add('season')
            ->add('type')
            ->add('points')
            ->add('wins')
            ->add('constructor')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Title::class,
        ]);
    }
}
