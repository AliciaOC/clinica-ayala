<?php

namespace App\Form;

use App\Entity\Horario;
use App\Entity\Terapeuta;
use App\Entity\Tratamiento;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrarTerapeutaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'mapped' => false, //no se mapea con la entidad
                'label' => 'Email*',
                'required' => true,
                'attr' => ['placeholder' => 'Introduzca el email del nuevo usuario'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('confirmacion', CheckboxType::class, [
                'mapped' => false, //no se mapea con la entidad
                'label' => 'La contraseña provisional será lo que haya antes del @ del email y se deberá cambiar tras el primer acceso. ',
                'required' => true,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('titulacion' , null, [
                'label' => 'Titulación*',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                ])
            ->add('nombre', null, [
                'label' => 'Nombre*',
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
                //Para ordenar los tratamientos por nombre
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                            ->orderBy('t.nombre', 'ASC');
                },
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
