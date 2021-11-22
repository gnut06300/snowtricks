<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture' , FileType::class ,[
                'attr' => ['autofocus' => true ],
                'label' => 'Votre avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        //'allowPortrait' => false,
                        //'minWidth' => 20000,
                        'maxSize' => '2048k',
                    ])
                ],
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
