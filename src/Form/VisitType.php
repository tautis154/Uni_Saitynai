<?php

namespace App\Form;

use App\Entity\Doctor;
use App\Entity\Medicine;
use App\Entity\Visit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class VisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('clientName')
            ->add('isAdmitted')
            ->add('isFinished')
            ->add('doctor', EntityType::class, [
                'class' => Doctor::class,
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('medicine', EntityType::class, [
                'class' => Medicine::class,
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotNull(),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Visit::class,
        ]);
    }
}
