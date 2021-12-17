<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Comments;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;

final class CommentsAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('email')
            ->add('description')
            ->add('publish')
            ->add('createdAt')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('from')
            ->add('email')
            ->add('description')
            ->add('publish',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
            ->add('createdAt')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {

    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ->add('email')
            ->add('description')
            ->add('publish')
            ->add('createdAt')
            ;
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        unset($actions['create']);
        unset($actions['edit']);

        return $actions;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('edit');
    }

    public function toString($object)
    {
        return $object instanceof Comments
            ? 'Comentario '.$object->getName()
            : 'Comentario'; // shown in the breadcrumb on the create view
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
    }

    public function getExportFields(): array
    {
        return ['id', 'name', 'email', 'description', 'publish'];
    }
}
