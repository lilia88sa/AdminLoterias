<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 12/14/2020
 * Time: 5:51 PM
 */

namespace App\Form;


use App\Entity\Comments;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
            'required'   => false,
                'label' => 'Nombre',
                'empty_data' => 'Usuario',
                'row_attr' => ['class' => 'form-control mb-3'],
                'attr' => ['placeholder' => 'Nombre']
              ])

            ->add('email', EmailType::class,[
                'required'   => true,
                'label' => 'Correo',
                'row_attr' => ['class' => 'form-control mb-3', 'placeholder' => 'Correo'],
                'attr' => ['placeholder' => 'Correo']
            ])
            ->add('description', TextareaType::class, [
                'required'   => true,
                'label' => 'Comentario',
                'row_attr' => ['class' => 'form-control mb-3', 'width' => '100%' ],
                'attr' => ['rows' => 5, 'placeholder' => 'Comentario']
            ])

            ->add('captcha', CaptchaType::class,[
                'required'   => true,
                'distortion' => false,
                'ignore_all_effects' => true,
                'label' => ' '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => Comments::class,
            'csrf_protection' => true
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'post';
    }
}