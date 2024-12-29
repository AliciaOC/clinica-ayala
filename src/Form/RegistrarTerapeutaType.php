<?php

namespace App\Form;

use App\Entity\Horario;
use App\Entity\Terapeuta;
use App\Entity\Tratamiento;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class RegistrarTerapeutaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulacion' , null, [
                'label' => 'Titulación',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                ])
            ->add('nombre', null, [
                'label' => 'Nombre',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                ])
            ->add('horario', EntityType::class, [
                'class' => Horario::class,
                'choice_label' => 'franja_horaria',
                'label' => 'Horario',
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('tratamientos', EntityType::class, [
                'class' => Tratamiento::class,
                'choice_label' => 'nombre',
                'label' => 'Tratamientos',
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terapeuta::class,
        ]);
    }
}
