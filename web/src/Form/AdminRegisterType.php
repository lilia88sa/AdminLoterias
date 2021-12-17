<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 07/09/2020
 * Time: 02:13
 */

namespace App\Form;

use App\Entity\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AdminRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if($options['action_type'] == 'register'){
            $builder
                ->add('name', TextType::class, [
                    'help' => 'Nombre Completo',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Nombre Completo'
                    ]
                ])
                ->add('email', EmailType::class, [
                    'help' => 'email',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Email'
                    ]
                ])
                ->add('password', PasswordType::class, [
                    'help' => 'Contraseña',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Contraseña'
                    ]
                ])
                ->add('username', TextType::class, [
                    'help' => 'Nombre de usuario',
                    'help_html' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Nombre de usuario'
                    ]
                ]);
        }elseif ($options['action_type'] == 'recover_password_email'){
            $builder
            ->add('email', EmailType::class, [
                'by_reference' => false,
                'help' => 'email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email'
                ]
            ]);
        }elseif ($options['action_type'] == 'recover_password_password'){
            $builder
                ->add('password', PasswordType::class, [
                    'help' => 'Contraseña',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Contraseña'
                    ]
                ])
                ;
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => User::class,
            'csrf_protection' => true,
            'action_type'     => 'register'
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'user';
    }
}