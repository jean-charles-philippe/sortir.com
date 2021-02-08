<?php

namespace App\Form;


use App\Entity\Location;
use App\Entity\Vacation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ["label"=>"Nom de la sortie"])
            ->add('vacation_date', DateTimeType::class, ["label"=>"Date et heure de la sortie"])
            ->add('vacation_limitDate', DateType::class, ["label"=>"Date limite d'inscription"])
            ->add('placeNumber', IntegerType::class, ["label"=>"Nombre de places"])
            ->add('duration', IntegerType::class, ["label"=>"Durée"])
            ->add('description', TextareaType::class, ["label"=>"Description et infos"])
            ->add('location', EntityType::class,[
                "class" => Location::class,
                'label' => 'Lieu',
                'choice_label' => 'name'
                 ])
            ->add('campus', TextType::class, ['attr' => ['disabled' => true]])
            ->add('save', SubmitType::class, [
                'label' => "Enregistrer",
                'attr'=> ['class'=>"btn btn-dark btn-lg"]
            ])
            ->add('saveAndAdd', SubmitType::class, [
                'label' => "Publier",
                'attr'=> ['class'=>"btn btn-dark btn-lg"]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vacation::class,
        ]);
    }
}
