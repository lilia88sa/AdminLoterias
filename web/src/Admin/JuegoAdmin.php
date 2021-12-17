<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Juego;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;
use Sonata\AdminBundle\Templating\TemplateRegistry;

final class JuegoAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('bote')
            ->add('publish')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('bote')
            ->add('fecha_bote')
            ->add('publish',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
            ->add('bote_destacado',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
           // ->add('imagen')
            ->add('bote')
            ->add('fecha_bote',DateTimePickerType::class,[
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
            ->add('publish',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
            ->add('orderElement')
            ->add('url')
            ->add('order_home')
            ->add('url_juego')
            ->add('bote_destacado',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))


            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
//            ->add('createdAt')
//            ->add('updatedAt')
//            ->add('createdBy')
//            ->add('updatedBy')
            ;
    }

    public function toString($object)
    {
        return $object instanceof Juego
            ? 'Juego '.$object->getName()
            : 'Juego'; // shown in the breadcrumb on the create view
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
    }
}
