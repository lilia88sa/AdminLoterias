<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Category;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class CategoryAdmin extends AbstractAdmin
{


    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('publish')
            ;
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();
        return $actions;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
    }

    public function toString($object)
    {
        return $object instanceof Category
            ? 'Categoría '.$object->getTitle()
            : 'Categoría'; // shown in the breadcrumb on the create view
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
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
                ->add('section.title')
                ->add('publish',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
                ->add('orderElement')
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
                ->add('section.title')
                ->add('publish')
                ->add('orderElement')
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
                ->add('orderElement')
                ->add('description',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->end()
                ->with('Ingles', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Ingles',
                    'class' => 'col-md-12'))
                ->add('titleEs')
                ->add('descriptionEs',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->end()
                ->add('publish')

            ;
        }else{
            $formMapper
                ->with('Español', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Español',
                    'class' => 'col-md-12'))
                ->add('title',null,['required' => true])
                ->add('orderElement')
                ->add('description',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->end()
                ->with('Ingles', array(
                    'translation_domain' => 'sonata_messages',
                    'label' => 'Ingles',
                    'class' => 'col-md-12'))
                ->add('titleEs')
                ->add('descriptionEs',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
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
            ->add('description', TemplateRegistry::TYPE_HTML, [])
            ->add('publish')
            ->add('orderElement')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('category_files', null, array(
                'template' => 'security/_show_images.html.twig'))
            ;
    }
}
