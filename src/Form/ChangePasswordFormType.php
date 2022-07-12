<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ChangePasswordFormType extends \Symfony\Component\Form\AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints'=> [
                    new NotBlank(),
                    new Length([
                        'min' => 8,
                        'max' => 255,
                        'minMessage' => "Lozinka moze imati najmanje 8 karaktera.",
                        'maxMessage' => "Lozinka moze imati najvise 255 karaktera."
                    ]),
                    new Type('string')
                ],
                'first_options'=>['label'=>'Nova lozinka'],
                'second_options'=>['label'=>'Ponovi lozinku'],
                'invalid_message'=>'Lozinke se ne poklapaju.'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }



}