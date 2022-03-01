<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class FormMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('media', FileType::class, [
                'label' => false,
                'required' => false,
                'data_class' => null,
                'attr' => array('class' => 'input-form form-control  form-class'),
                'constraints' => [
                    new File([
                        'mimeTypes' => [ // We want to let upload only jpg, jpeg or png files
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => "Veuillez soumettre un fichier de type image (jpg, jpeg ou png)",
                    ])
                ]
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
