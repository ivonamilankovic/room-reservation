<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordFormType extends \Symfony\Component\Form\AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'=>['label'=>'Nova lozinka'],
                'second_options'=>['label'=>'Ponovi lozinku'],
                'invalid_message'=>'Lozinke se ne poklapaju.'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }



}