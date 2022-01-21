<?php

namespace App\Form;

use App\Entity\Trick;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category')
            ->add('name', TextType::class)
            ->add('description')
            ->add('upload_file', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => true,
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
            ->add('video_name', TextType::class, ['mapped' => false])
            ->add('video_url', TextType::class, ['mapped' => false])
            ->add('Valider', SubmitType::class);
    }
}
