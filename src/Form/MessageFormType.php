<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'user',
                EntityType::class,
                [
                    'class' => User::class,
                    'choice_label' => function (User $user) {
                        return $user->getEntreprise() . " - " . $user->getEmail() . ".";
                    },
                    'query_builder' => function (UserRepository $rp) {
                        return $rp->getUsers();
                    }
                ],
            )
            ->add('sujet', TextType::class, ['label' => 'Sujet'])
            ->add('pieceJointe', FileType::class, ['label' => 'Piece Jointe(Facultatif)', 'required' => false])
            ->add('contenu', TextType::class, ['label' => 'Message'])
            ->add('envoyer', SubmitType::class, ['attr' => ['class' => "btn-danger"]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
