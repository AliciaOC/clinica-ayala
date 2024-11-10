<?php

namespace App\Form;

use App\Entity\Horario;
use App\Entity\Terapeuta;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NuevoHorarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('franja_horaria')
            ->add('terapeutas', EntityType::class, [
                'class' => Terapeuta::class,
                'choice_label' => 'nombre',
                'multiple' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Añadir horario',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Horario::class,
        ]);
    }
}
