<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends \Symfony\Component\Form\AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label'=>"Ime"])
            ->add('lastName', TextType::class, ["label"=>"Prezime"])
            ->add('email', EmailType::class, ["label"=>"Email adresa"])
            ->add('sector', null, ['label'=>'Sektor'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}