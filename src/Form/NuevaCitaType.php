<?php

namespace App\Form;

use App\Entity\Cita;
use App\Entity\Cliente;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NuevaCitaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('fecha', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha y hora*',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('motivo', null, [
                'label' => 'Motivo de la cita',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('cliente', EntityType::class, [
                'class' => Cliente::class,
                'choices' => $options['terapeuta']->getClientes(), // Filtrar solo los clientes del terapeuta
                'choice_label' => 'nombre',
                'label' => 'Cliente*',
                'placeholder' => 'Selecciona un cliente de la lista.',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Guardar',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cita::class,
        ]);
        $resolver->setRequired('terapeuta');
    }
}
