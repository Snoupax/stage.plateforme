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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfilFirstEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre Email',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre email',
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter {{ limit }} caractères.',
                        'max' => 4096,
                    ])
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre nom'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit contenir {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Votre prenom',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre prenom'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit contenir {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'Votre entreprise',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez le nom de votre entreprise'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom de votre entreprise doit contenir {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('site_web', TextType::class, [
                'label' => 'Votre site',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre site web'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre site web doit contenir {{ limit }} caractères'
                    ])
                ]
            ])

            ->add('modifier', SubmitType::class, ['attr' => ['class' => 'btn-success']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
