<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un email',
                    ])
                ]
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'Entreprise',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Entrez un nom d'entreprise"
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => "Le nom de l'entreprise doit contenir {{ limit }} caractères"
                    ])
                ]
            ])
            ->add('site_web', TextType::class, [
                'label' => 'Site Web',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez le site web'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le site web doit contenir {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('envoyer', SubmitType::class, ['attr' => ['class' => "btn-success"]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
