<?php

namespace App\Form;

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
            ->add('titulacion')
            ->add('nombre')
            ->add('horario')
            ->add('tratamientos', EntityType::class, [
                'class' => Tratamiento::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false,
            ])
            ->add('email', EmailType::class)
            ->add('confirmacion', CheckboxType::class, [
                'mapped' => false, //no se mapea con la entidad
                'label' => 'Informe al nuevo Terapeuta que su contraseña provisional es lo que hay antes del @ en su email y que el programa le pedirá cambiar la contraseña en el primer acceso.',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Guardar',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terapeuta::class,
        ]);
    }
}
