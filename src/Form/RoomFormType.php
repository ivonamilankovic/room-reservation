<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomFormType extends \Symfony\Component\Form\AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, ['label'=> "Naziv sale"])
            ->add('seat_number', NumberType::class, ['label'=>"Kapacitet"])
            ->add('city', TextType::class, ['label'=>"Grad"])
            ->add('street', TextType::class, ['label'=>'Ulica']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>Room::class
        ]);
    }

}