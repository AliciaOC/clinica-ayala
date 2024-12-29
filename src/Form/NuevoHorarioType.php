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
            ->add('franja_horaria', null, [
                'label' => 'Franja horaria',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('terapeutas', EntityType::class, [
                'class' => Terapeuta::class,
                'choice_label' => 'nombre',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Guardar',
                'attr' => ['class' => 'btn btn-primary'],
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
