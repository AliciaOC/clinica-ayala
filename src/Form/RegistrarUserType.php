<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrarUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
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

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
