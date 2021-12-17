<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\KeyWords;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class KeyWordsAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('description')
            ->add('isGlossary')
            ->add('createdAt')
            ->add('updatedAt')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('title')
            ->add('isGlossary',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
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

    public function toString($object)
    {
        return $object instanceof KeyWords
            ? 'Palabra Clave '.$object->getTitle()
            : 'Palabra Clave'; // shown in the breadcrumb on the create view
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Español', array(
                'translation_domain' => 'sonata_messages',
                'label' => 'Español',
                'class' => 'col-md-12'))
            ->add('title',null,['required' => true])
            ->add('description',TextareaType::class,[
                    'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
            )
            ->end()
            ->with('Ingles', array(
                'translation_domain' => 'sonata_messages',
                'label' => 'Ingles',
                'class' => 'col-md-12'))
            ->add('titleES')
            ->add('descriptionES',TextareaType::class,[
                    'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
            )
            ->end()
            ->add('isGlossary')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('titleES')
            ->add('description', TemplateRegistry::TYPE_HTML, [])
            ->add('descriptionES', TemplateRegistry::TYPE_HTML, [])
            ->add('isGlossary')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('keyWordsFiles', null, array(
                'template' => 'security/_show_images.html.twig'))
            ;
    }
}
