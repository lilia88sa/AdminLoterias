<?php

namespace App\Form;

use App\Entity\AtencionPoblacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class AtencionPoblacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['action_type'] == 'queja'){
            $builder
                ->add('municipio', ChoiceType::class, [
                    'required'   => true,
                    'choices'  => [
                        'Seleccione...' => '0',
                        'PLAYA' => '2301',
                        'PLAZA DE LA REVOLUCION' => '2302',
                        'CENTRO HABANA' => '2303',
                        'LA HABANA VIEJA' => '2304',
                        'REGLA' => '2305',
                        'LA HABANA DEL ESTE' => '2306',
                        'GUANABACOA' => '2307',
                        'SAN MIGUEL DEL PADRON' => '2308',
                        'DIEZ DE OCTUBRE' => '2309',
                        'CERRO' => '2310',
                        'MARIANAO' => '2311',
                        'LA LISA' => '2312',
                        'BOYEROS' => '2313',
                        'ARROYO NARANJO' => '2314',
                        'COTORRO' => '2315'
                    ],
                    'label' => 'Municipio *',
                    'row_attr' => ['class' => 'form-control rounded-0'],
                ])
                ->add('nivel', ChoiceType::class, array(
                    'choices' => array(
                        'Municipal' => true,
                        'Provincial' => false,
                    ),
                    'expanded' => true, // use a radio list instead of a select input
                ))
                ->add('nombre',TextType::class, [
                    'required'   => true,
                    'label' => 'Nombre *',
                    'row_attr' => ['class' => 'form-control rounded-0'],
                ])
                ->add('direccion', TextareaType::class, [
                    'required'   => true,
                    'label' => 'Dirección *',
                    'attr' => ['rows' => 10, 'cols' => "100%"],
                    'row_attr' => ['class' => 'form-control rounded-0'],
                ])
                ->add('clasificacion', ChoiceType::class, [
                    'required'   => true,
                    'choices'  => [
                        'Seleccione...' => '0',
                        'Solicitud' => '1',
                        'Queja' => '2',
                        'Denuncia' => '3',
                        'Sugerencia' => '6'
                    ],
                    'label' => 'Clasificación *',
                    'row_attr' => ['class' => 'form-control rounded-0'],
                ])
                ->add('resumen', TextareaType::class, [
                    'required'   => true,
                    'label' => 'Resumen *',
                    'attr' => ['rows' => 10, 'cols' => "100%"],
                    'row_attr' => ['class' => 'form-control rounded-0'],
                ])
                ->add('correo', EmailType::class,[
                    'required'   => true,
                    'label' => 'Correo *',
                    'row_attr' => ['class' => 'form-control rounded-0']
                ])
                ->add('telefono',TextType::class, [
                    'required'   => false,
                    'label' => 'Teléfono',
                    'row_attr' => ['class' => 'form-control rounded-0'],
                ])
                ->add('notificar', ChoiceType::class, array(
                    'label' => ' ',
                    'choices' => array(
                        'Correo' => 0,
                        'Móvil' => 1,
                    ),
                    'expanded' => true,
                    'multiple' => true, // use a radio list instead of a select input
                ))
                ->add('publico', ChoiceType::class, array(
                    'label' => ' ',
                    'choices' => array(
                        'Si' => true,
                        'No' => false,
                    ),
                    'expanded' => true, // use a radio list instead of a select input
                ))
                ->add('captcha', CaptchaType::class,[
                    'required'   => true,
                    'row_attr' => ['class' => 'form-control rounded-0'],
                    'distortion' => false,
                    'ignore_all_effects' => true,
                    'attr' => ['placeholder' => 'Inserte el código']
                ])
            ;
        }else{
            $builder
                ->add('codigo', TextType::class, [
                    'required'   => true,
                    'label' => 'Inserte código',
                    'row_attr' => ['class' => 'form-control'],
                ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AtencionPoblacion::class,
            'csrf_protection' => true,
            'action_type' => null
        ]);
    }
}
