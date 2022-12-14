<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DemandeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('sujet', TextType::class, [
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
            ->add('pieceJointe', FileType::class, [
                'label' => 'Piece Jointe',
                'required' => false,
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Message',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez le message de votre demande'
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le message de votre demande doit contenir {{ limit }} caractères'
                    ])
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('envoyer', SubmitType::class, ['attr' => ['class' => "btn-success"]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
