<?php

namespace App\Form;

use App\Entity\Terapeuta;
use App\Entity\Tratamiento;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NuevoTratamientoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('terapeutas', EntityType::class, [
                'class' => Terapeuta::class,
                'choice_label' => 'nombre',
                'label' => 'Terapeuta',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Añadir',
                'attr' => ['class' => 'boton'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tratamiento::class,
        ]);
    }
}
