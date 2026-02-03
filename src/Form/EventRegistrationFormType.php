<?php

namespace App\Form;

use App\Entity\ToRegister;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('whatsappNumber', TelType::class, [
                'label' => 'Numéro WhatsApp (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => '+33 6 12 34 56 78',
                    'class' => 'form-control',
                ],
                'help' => 'Pour recevoir les informations de l\'événement par WhatsApp',
            ])
            ->add('latitude', HiddenType::class, [
                'required' => false,
            ])
            ->add('longitude', HiddenType::class, [
                'required' => false,
            ])
            ->add('shareLocation', CheckboxType::class, [
                'label' => 'Partager ma position GPS avec l\'organisateur',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'help' => 'Permet à l\'organisateur de vous localiser le jour de l\'événement',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ToRegister::class,
        ]);
    }
}
