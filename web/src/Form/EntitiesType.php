<?php

namespace App\Form;

use App\Entity\Entities;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class EntitiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comercialName', TextType::class, [
                'required'   => true,
                'label' => 'Nombre Comercial',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('name',TextType::class, [
                'required'   => true,
                'label' => 'Nombre Completo',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('code',TextType::class, [
                'required'   => false,
                'label' => 'Código',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('socialReason', TextareaType::class, [
                'required'   => false,
                'label' => 'Razón Social',
                'attr' => ['rows' => 5],
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('schedule',TextType::class, [
                'required'   => false,
                'label' => 'Horarios',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('schedulePublic',TextType::class, [
                'required'   => false,
                'label' => 'Horario Atención Público',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('postalCode',TextType::class, [
                'required'   => false,
                'label' => 'Código Postal',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('phone',TextType::class, [
                'required'   => false,
                'label' => 'Telefonos',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('email',EmailType::class, [
                'required'   => true,
                'label' => 'Correo Electrónico',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('website', UrlType::class, [
                'required'   => false,
                'label' => 'Sitio Web(Opcional)',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('description', TextareaType::class, [
                'required'   => false,
                'label' => 'Descripción de la entidad o Negocio',
                'attr' => ['rows' => 12,'class' => 'ckeditor'],
                'row_attr' => ['class' => 'form-control mb-3 ckeditor'],
            ])
            ->add('serviceDescription', TextareaType::class, [
                'required'   => true,
                'label' => 'Servicios Prestados',
                'attr' => ['rows' => 12,'class' => 'ckeditor' ],
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('entityType', ChoiceType::class, [
                'required'   => true,
                'choices'  => [
                    'ESTATAL' => Entities::ENTITY_TYPE_ESTATAL,
                    'PARTICULAR' => Entities::ENTITY_TYPE_PARTICULAR,
                ],
                'label' => 'Tipo',
                'row_attr' => ['class' => 'form-control mb-3'],
            ])
            ->add('captcha', CaptchaType::class,[
                'required'   => true,
                'row_attr' => ['class' => 'form-control mb-3'],
                'distortion' => false,
                'ignore_all_effects' => true,
                 'attr' => ['placeholder' => 'Inserte el código']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entities::class,
            'csrf_protection' => true
        ]);
    }
}
