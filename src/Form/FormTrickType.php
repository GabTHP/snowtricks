<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FormTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'category',
            ChoiceType::class,
            array(
                'choices' => [
                    'Choice1' => 'Choice1',
                    'Choice2' => 'Choice2',
                    'Choice3' => 'Choice3',
                    'Choice4' => 'Choice4',
                ],
                'label' => false,
                'attr' => array('class' => 'input-form form-control form-class')
            )
        )
            ->add(
                'name',
                TextType::class,
                array(
                    'label' => false,
                    'attr' => array('class' => 'input-form form-control form-class')


                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'label' => false,
                    'attr' => array('class' => 'input-form form-control form-class-area')
                )
            )
            ->add('upload_file', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
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
                ],
            ])
            ->add('video_name', TextType::class, [
                'mapped' => false,
                'attr' => array('class' => 'input-form form-control  form-class'),
                'label' => false,
            ])
            ->add('video_url', TextType::class, [
                'mapped' => false,
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
            'data_class' => Trick::class,
        ]);
    }
}
