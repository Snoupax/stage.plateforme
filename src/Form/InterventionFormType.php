<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add(
            'users',
            EntityType::class,
            [
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
            ->add('sujet')
            ->add('messageOptionnel');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
