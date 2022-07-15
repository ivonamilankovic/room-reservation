<?php

namespace App\Form;

use App\Entity\Meeting;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\UserInMeetingRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        if($options['room_field']) {
            $builder
                ->add('room', EntityType::class, [
                    'label' => 'Sala',
                    'class' => Room::class,
                    'placeholder' => 'Odaberi salu',
                    'data' => $options['selected_room']
                ]);
        }
        if($options['date_set']){
            $builder
                ->add('start', DateTimeType::class, [
                    'label'=>'Pocetno vreme sastanka:'
                ])
                ->add('end', DateTimeType::class, [
                    'label'=>'Krajnje vreme sastanka:'
                ])
            ;
        }else{
            $builder
                ->add('start', DateTimeType::class, [
                    'label'=>'Pocetno vreme sastanka:',
                    'data' => new \DateTime()
                ])
                ->add('end', DateTimeType::class, [
                    'label'=>'Krajnje vreme sastanka:',
                    'data' => new \DateTime('now + 1 hour')
                ])
            ;
        }

        $builder

            ->add('description', TextareaType::class, ['label'=>'Informacije o sastanku:'])
            ->add('users', EntityType::class, [
                'label'=>'Odaberi kolege koje zelis da prisustvuju: ',
                'help' => '*broj osoba treba da bude jednak sa kapacitetom sale',
                'multiple' => true,
                'expanded' => true,
                'class' => User::class,
                'choices' => $this->userRepository->findUsersForMeeting($options['loggedUser']),
                'choice_label' => function (User $user){
                    return $user->getFullName();
                },
                'mapped' => false,
                'data' => $this->userRepository->findUsersOnMeeting(['meeting'=>$options['meeting_id']])
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> Meeting::class,
            'loggedUser' => 0,
            'selected_room' => null,
            'room_field' => false,
            'meeting_id' => null,
            'date_set' => false
        ]);
        $resolver->setAllowedTypes('loggedUser', 'int');
    }

}