<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormVideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('video_name', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => array('class' => 'input-form form-control  form-class'),
                'label' => false,
            ])
            ->add('video_url', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => array('class' => 'input-form form-control  form-class'),
                'label' => false,
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => array('class' => 'border-better btn btn-primary text-white')
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
