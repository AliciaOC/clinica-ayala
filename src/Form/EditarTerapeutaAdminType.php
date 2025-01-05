<?php

namespace App\Form;

use App\Entity\Cliente;
use App\Entity\Horario;
use App\Entity\Terapeuta;
use App\Entity\Tratamiento;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditarTerapeutaAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => false,
                'mapped' => false,
                'data' => $options['email'],
                'attr' => ['class' => 'form-control'],
            ])            
            ->add('titulacion', null, [
                'label' => 'Titulación',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('nombre', null, [
                'label' => 'Nombre',
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
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.nombre', 'ASC');
                },
            ])
            ->add('clientes', EntityType::class, [
                'class' => Cliente::class,
                'choice_label' => 'nombre',
                'label' => 'Clientes',
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'form-select'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nombre', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terapeuta::class,
            'email' => null,
        ]);
    }
}
