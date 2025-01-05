<?php

namespace App\Form;

use App\Entity\Terapeuta;
use App\Entity\Tratamiento;
use Doctrine\ORM\EntityRepository;
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
            ->add('nombre', null, [
            'label' => 'Nombre del tratamiento*',
            'required' => true,
            'attr' => ['placeholder' => 'Introduce el nombre del tratamiento'],
            'attr' => ['class' => 'form-control'],
            ])
            ->add('descripcion', null, [
            'label' => 'Descripción*',
            'required' => true,
            'attr' => ['placeholder' => 'Introduce la descripción del tratamiento'],
            'attr' => ['class' => 'form-control'],
            ])
            ->add('terapeutas', EntityType::class, [
                'class' => Terapeuta::class,
                'choice_label' => 'nombre',
                'label' => 'Terapeuta',
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'form-select'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                            ->orderBy('t.nombre', 'ASC');
                },
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Añadir',
                'attr' => ['class' => 'btn btn-primary'],
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
