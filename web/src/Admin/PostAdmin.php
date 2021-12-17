<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Post;
use Carbon\Carbon;
use Doctrine\DBAL\Types\DateTimeType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateTimePickerType;
use Sonata\Form\Type\DateTimeRangePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichFileType;

final class PostAdmin extends AbstractAdmin
{
    public $baseRouteName = 'admin.section.admin_app_post';

    public function toString($object)
    {
        return $object instanceof Post
            ? 'Artículo '.$object->getTitle()
            : 'Artículo'; // shown in the breadcrumb on the create view
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('clasification')
            ->add('publish')
            ->add('category')
            ->add('createdAt',
                'doctrine_orm_datetime_range', [
                    'field_type'=> DatePickerType::class,
                ])
            ->add('createdBy')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $securityContext = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');
        if($securityContext->isGranted('ROLE_EDITOR') ||
            $securityContext->isGranted('ROLE_SUPER_ADMIN') ||
            $securityContext->isGranted('ROLE_ADMIN') ){
            $listMapper
                ->add('id')
                ->add('title')
                ->add('category')
                ->add('author')
                ->add('clasification')
                ->add('publish',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
                ->add('createdAt')
                ->add('updatedAt')
                ->add('createdBy')
                ->add('updatedBy')
                ->add('_action', null, [
                    'actions' => [
                        'show' => [],
                        'edit' => [],
                        'delete' => [],
                    ],
                ]);

        }else{
            $listMapper
                ->add('id')
                ->add('title')
                ->add('category')
                ->add('author')
                ->add('clasification')
                ->add('publish')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('createdBy')
                ->add('updatedBy')
                ->add('_action', null, [
                    'actions' => [
                        'show' => [],
                        'edit' => [],
                        'delete' => [],
                    ],
                ]);
        }

    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $securityContext = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');
        if($securityContext->isGranted('ROLE_EDITOR') ||
            $securityContext->isGranted('ROLE_SUPER_ADMIN') ||
            $securityContext->isGranted('ROLE_ADMIN') ){
            $formMapper
                ->with('Español', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Español',
                    'class' => 'col-md-12'))
                ->add('title',null,['required' => true])
                ->add('sumary')
                ->add('description',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->end()
                ->with('Ingles', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Ingles',
                    'class' => 'col-md-12'))
                ->add('titleEs')
                ->add('sumaryEs')
                ->add('descriptionEs',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->end()
                ->with('Otros Datos', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Otros Datos',
                    'class' => 'col-md-12'))
                ->add('audio')
                ->add('video')
                ->add('author')
                ->add('clasification', ChoiceType::class,[
                    'translation_domain' => 'sonata_messages',
                    'choices' => [
                        'Sin Clasificar' => null,
                        'Impacto' => Post::CLASIFICATION_IMPACT,
                        'Relevante' =>Post::CLASIFICATION_RELEVANT
                    ],
                    'empty_data' => null,
                    'required' => false,
                    'placeholder' => null,
                ])
                ->add('publish')
                ->add('category')
                ->add('pdf', CollectionType::class, array(
        'required' => false,
        'by_reference' => false,
        'label' => 'PDF/TXT/WORD'
    ), array(
        'edit' => 'inline',
        'inline' => 'table')
    )
                ->end()
                ->with('Datos Opcionales', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Datos Opcionales',
                    'class' => 'col-md-6'))
                   ->add('optionalDate',DateTimePickerType::class,[
                           'required'              => false,
                           'dp_side_by_side'       => true,
                           'dp_use_current'        => false,
                           'dp_use_seconds'        => false,
                           'dp_collapse'           => true,
                           'dp_calendar_weeks'     => false,
                           'dp_view_mode'          => 'days',
                           'dp_min_view_mode'      => 'days',
                           'translation_domain' => 'sonata_messages']
                   )
                ->end()
                ->with('Fecha de Publicación (Opcional)', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Fecha de Publicación (Opcional)',
                    'class' => 'col-md-6'))
                ->add('postPublish',DateTimePickerType::class,[
                        'required'              => false,
                        'dp_side_by_side'       => true,
                        'dp_use_current'        => false,
                        'dp_use_seconds'        => false,
                        'dp_collapse'           => true,
                        'dp_calendar_weeks'     => false,
                        'dp_view_mode'          => 'days',
                        'dp_min_view_mode'      => 'days',
                        'translation_domain' => 'sonata_messages']
                )
                ->end()
            ;
        }else{
            $formMapper
                ->with('Español', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Español',
                    'class' => 'col-md-12'))
                ->add('title',null,['required' => true])
                ->add('sumary')
                ->add('description',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->end()
                ->with('Ingles', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Ingles',
                    'class' => 'col-md-12'))
                ->add('titleEs')
                ->add('sumaryEs')
                ->add('descriptionEs',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->end()
                ->with('Otros Datos', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Otros Datos',
                    'class' => 'col-md-12'))
                ->add('audio')
                ->add('video')
                ->add('author')
                ->add('clasification', ChoiceType::class,[
                    'translation_domain' => 'sonata_messages',
                    'choices' => [
                        'Sin Clasificar' => null,
                        'Impacto' => Post::CLASIFICATION_IMPACT,
                        'Relevante' =>Post::CLASIFICATION_RELEVANT
                    ],
                    'empty_data' => null,
                    'required' => false,
                    'placeholder' => null,
                ])
                ->add('category')
                ->add('pdf', CollectionType::class, array(
                    'required' => false,
                    'by_reference' => false,
                    'label' => 'Media items'
                ), array(
                        'edit' => 'inline',
                        'inline' => 'table')
                )
                ->end()
                ->with('Datos Opcionales', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Datos Opcionales',
                    'class' => 'col-md-6'))
                ->add('optionalDate',DateTimePickerType::class,[
                        'required'              => false,
                        'dp_side_by_side'       => true,
                        'dp_use_current'        => false,
                        'dp_use_seconds'        => false,
                        'dp_collapse'           => true,
                        'dp_calendar_weeks'     => false,
                        'dp_view_mode'          => 'days',
                        'dp_min_view_mode'      => 'days',
                        'translation_domain' => 'sonata_messages']
                )
                ->end()


            ;
        }
        if($securityContext->isGranted('ROLE_SUPER_ADMIN') ||
            $securityContext->isGranted('ROLE_ADMIN') ) {
            $formMapper
                ->add('orderClasification');
        }

    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('sumary')
            ->add('description', TemplateRegistry::TYPE_HTML, [])
            ->add('titleEs')
            ->add('sumaryEs')
            ->add('descriptionEs', TemplateRegistry::TYPE_HTML, [])
            ->add('audio')
            ->add('video')
            ->add('author')
            ->add('clasification')
            ->add('publish')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('createdBy')
            ->add('updatedBy')

            ->add('post_files', null, array(
                'template' => 'security/_show_images.html.twig'))
            ;
    }

    public function preUpdate($object)
    {
        $em =  $this->getConfigurationPool()->getContainer()->get(
            'doctrine.orm.entity_manager');
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $original = $em->getUnitOfWork()->getOriginalEntityData($object);
        $updated = Carbon::instance(new \DateTime('now'));
        //UPDATE WHEN TRANSLATION HAPPEN
        if($original['titleEs'] != $object->getTitleEs() ||
            $original['descriptionEs'] != $object->getDescriptionEs() ||
            $original['sumaryEs'] != $object->getSumaryEs()){
            $object->setTranslationUpdate($updated);
            $object->setLastUserTranslation($user->getName());
        }
        parent::preUpdate($object); // TODO: Change the autogenerated stub
    }
}
