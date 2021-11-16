<?php

namespace App\Form;

use App\Entity\Doctor;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class DoctorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fkUser', EntityType::class, [
                'class' => User::class,
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotNull(),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Doctor::class,
        ]);
    }
}
