<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProfileFormType extends \Symfony\Component\Form\AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label'=>"Ime"])
            ->add('lastName', TextType::class, ["label"=>"Prezime"])
            ->add('email', EmailType::class, ["label"=>"Email adresa"])
            ->add('image', FileType::class,[
                'label' =>'Profilna slika (opciono)',
                'mapped' => false,
                'required' => false,
                'constraints' =>[
                    new Image([
                        'maxSize' => '10M'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}