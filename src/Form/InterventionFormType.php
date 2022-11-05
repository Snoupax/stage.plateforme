<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class InterventionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add(
            'users',
            EntityType::class,
            [
                'label' => 'Entreprise',
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getEntreprise() . " - " . $user->getEmail() . ".";
                },
                'multiple' => true,
                'query_builder' => function (UserRepository $rp) {
                    return $rp->getUsers();
                }
            ],
        )
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez le sujet de votre demande'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le sujet de votre demande doit contenir {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('messageOptionnel', TextareaType::class, [
                'label' => 'Message (Opt)',
                'required' => false,
                'attr' => ['class' => 'form-control']

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
