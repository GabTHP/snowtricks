<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, array(
                'label' => false,
                'attr' => array('class' => 'input-form form-control form-class width-input')
            ))
            ->add(
                'ask_rest',
                SubmitType::class,
                array(
                    'label' => "Demander un nouveau mot de passe",
                    'attr' => array('class' => 'border-better btn btn-primary')
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
