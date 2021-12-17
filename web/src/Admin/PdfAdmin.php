<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Pdf;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Vich\UploaderBundle\Form\Type\VichFileType;

final class PdfAdmin extends AbstractAdmin
{

    public function toString($object)
    {
        return $object instanceof Pdf
            ? 'PDF'
            : 'PDF'; // shown in the breadcrumb on the create view
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
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
            $listMapper
                ->add('id')
                ->add('title')
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


    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
            $formMapper
                ->add('title')
                ->add('files', VichFileType::class, [
                    'required' => false,
                    'allow_delete' => true,
                    'delete_label' => 'Eliminar',
                    'download_label' => 'Descargar',
                    'download_uri' => true,
                    'asset_helper' => true,
                ])
//                ->add('category', ModelListType::class,[
//                    'required'      => false,
//                    'btn_add'       => 'Add',      //Or you can specify a custom label
//                    'btn_catalogue' => 'sonata_messages'
//                ],
//                    [
//                        'edit'          => 'inline',
//                        'inline'        => 'standard',
//                    ])
            ;

    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('createdBy')
            ->add('updatedBy')
            ;
    }
}
