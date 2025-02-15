<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CambioPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nuevaPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Nueva contraseña*',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('confirmacionPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Confirmar contraseña*',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['class' => 'login-change_password'],
        ]);
    }
}
