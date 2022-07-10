<?php

namespace App\Form;

use App\Entity\Meeting;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingFormType extends \Symfony\Component\Form\AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateTimeType::class, [
                'label'=>'Pocetno vreme sastanka:',
                'data' => new \DateTime()
            ])
            ->add('end', DateTimeType::class, [
                'label'=>'Krajnje vreme sastanka:',
                'data' => new \DateTime('now + 1 hour')
            ])
            ->add('description', TextareaType::class, ['label'=>'Informacije o sastanku:'])
            ->add('users', EntityType::class, [
                'label'=>'Odaberi kolege koje zelis da prisustvuju:',
                'multiple' => true,
                'expanded' => true,
                'class' => User::class,
                'choices' => $this->userRepository->findUsersForMeeting($options['loggedUser']),
                'choice_label' => function (User $user){
                    return $user->getFullName();
                },
                'mapped' => false

            ]);

        //TODO da ne ispise prijavljenog usera na formi -->
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> Meeting::class,
            'loggedUser' => 0,
        ]);
        $resolver->setAllowedTypes('loggedUser', 'int');
    }

}