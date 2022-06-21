<?php

namespace App\Form;

use App\Entity\Meeting;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingFormType extends \Symfony\Component\Form\AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateTimeType::class, ['label'=>'Pocetno vreme sastanka:'])
            ->add('end', DateTimeType::class, ['label'=>'Krajnje vreme sastanka:'])
            ->add('description', TextareaType::class, ['label'=>'Informacije o sastanku:'])
            ->add('users', EntityType::class, [
                'label'=>'Odaberi kolege koje zelis da prisustvuju:',
                'multiple' => true,
                'expanded' => true,
                'class' => User::class,
                'choice_label' => 'email',
                'mapped' => false

            ]);

        //TODO da ne ispise prijavljenog usera na formi -->
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> Meeting::class,
        ]);
    }

}