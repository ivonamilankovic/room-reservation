<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends \Symfony\Component\Form\AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, ['label'=>"Ime"])
            ->add('last_name', TextType::class, ["label"=>"Prezime"])
            ->add('email', EmailType::class, ["label"=>"Email adresa"])
            ->add('sector', null, ['label'=>'Sektor']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>User::class]);
    }
}