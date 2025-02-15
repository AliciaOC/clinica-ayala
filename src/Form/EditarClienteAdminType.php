<?php

namespace App\Form;

use App\Entity\Cliente;
use App\Entity\Terapeuta;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditarClienteAdminType extends AbstractType
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
            ->add('nombre', null, [
                'label' => 'Nombre',
                'attr' => ['class' => 'form-control'],
            ]) 
            ->add('terapeutas', EntityType::class, [
                'class' => Terapeuta::class,
                'choice_label' => 'nombre',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => ['class' => 'form-select'],
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
            'data_class' => Cliente::class,
            'email' => null,
        ]);
    }
}
