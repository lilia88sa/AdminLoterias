<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\OrderClasification;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class OrderClasificationAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('name')
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

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('createdBy')
            ->add('updatedBy')
            ;
    }

    public function toString($object)
    {
        return $object instanceof OrderClasification
            ? 'Clasificación '.$object->getName()
            : 'Clasificación'; // shown in the breadcrumb on the create view
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
    }
}
