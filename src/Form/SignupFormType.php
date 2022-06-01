<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignupFormType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name',TextType::class, ['label'=> "Ime"])
            ->add('last_name', TextType::class, ['label'=>"Prezime"])
            ->add('email', EmailType::class, ['label'=>"Email adresa"])
            ->add('password', PasswordType::class, ['label'=>'Lozinka'])
            ->add('sector', null, [
                'label'=>'Sektor',
                'placeholder'=>"Odaberi svoj sektor"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>User::class
        ]);
    }

}