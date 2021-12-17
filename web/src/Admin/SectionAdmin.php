<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Section;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class SectionAdmin extends AbstractAdmin
{
//    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
//    {
//
//        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
//            return;
//        }
//        $admin = $this->isChild() ? $this->getParent() : $this;
//        $id = $admin->getRequest()->get('id');
//        $entity = $this->getObject($id);
//        $menu->addChild('Ver Sección '.$entity->getTitle(), $admin->generateMenuUrl('show', ['id' => $id]));
//
//        if ($this->isGranted('EDIT')) {
//            $menu->addChild('Editar Sección '.$entity->getTitle(), $admin->generateMenuUrl('edit', ['id' => $id]));
//        }
//
//        if ($this->isGranted('LIST')) {
//          $menu->addChild('Gestionar Categorías',$this->getChild('admin.category')->generateMenuUrl('list', array('id' => $id)) /*array('uri' => $childAdmin->generateUrl('list', array('id' => $id)))*/ /*$admin->generateMenuUrl('admin_app_post_list', ['id' => $id])*/);
//
//        }
//    }

    public function toString($object)
    {
        return $object instanceof Section
            ? 'Sección '.$object->getTitle()
            : 'Sección'; // shown in the breadcrumb on the create view
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
            ->add('publish')
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
                ->add('publish',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
//                ->add('orderElement')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('createdBy')
                ->add('updatedBy')
                ->add('cliente')
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
                ->add('publish')
//                ->add('orderElement')
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
//                ->with('Español', array(
//                    'translation_domain' => 'sonata_messages',
//                    'label' => 'Español',
//                    'class' => 'col-md-12'))
                ->add('title',null,['required' => true])
//                ->add('orderElement')
                ->add('description',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
//                ->end()
//                ->with('Ingles', array(
//                    'translation_domain' => 'sonata_messages',
//                    'label' => 'Ingles',
//                    'class' => 'col-md-12'))
//                ->add('titleEs')
//                ->add('descriptionEs',TextareaType::class,[
//                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
//                )
//                ->end()
                ->add('publish')
                ->add('cliente')
            ;
        }else{
            $formMapper
//                ->with('Español', array(
//                    'translation_domain' => 'sonata_messages',
//                    'label' => 'Español',
//                    'class' => 'col-md-12'))
                ->add('title',null,['required' => true])
//                ->add('orderElement')
                ->add('description',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->add('cliente')
//                ->end()
//                ->with('Ingles', array(
//                    'translation_domain' => 'sonata_messages',
//                    'label' => 'Ingles',
//                    'class' => 'col-md-12'))
//                ->add('titleEs')
//                ->add('descriptionEs',TextareaType::class,[
//                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
//                )
//                ->end()
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
//            ->add('orderElement')
            ->add('description', TemplateRegistry::TYPE_HTML, [])
            ->add('cliente')
//            ->add('titleEs')
//            ->add('descriptionEs', TemplateRegistry::TYPE_HTML, [])
            ->add('publish')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('section_files', null, array(
                'template' => 'security/_show_images.html.twig'))
            ;
    }
}
