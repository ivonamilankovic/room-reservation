<?php

namespace App\Form;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchRoomByCityFormType extends \Symfony\Component\Form\AbstractType
{
    private $roomRepository;

    public function __construct(RoomRepository $roomRepository){
        $this->roomRepository = $roomRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->roomRepository->findAllCities();
        $new = [];
        foreach ($choices as $choice){
            $new[] =  $choice['city'];
        }

        $builder
            ->add('search', ChoiceType::class,[
                'label_attr' => ['class' => 'd-none'],
                'choices' => $new,
                'choice_label' => function ($value) {
                    return $value;
                },
                'placeholder' => 'Pretraga po gradovima...',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}