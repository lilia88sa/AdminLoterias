<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Cliente;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichFileType;



final class ClienteAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('nombre')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('nombre')
            ->add('contact')
            ->add('phone')
            ->add('publish',TemplateRegistry::TYPE_BOOLEAN, array('editable' => true))
//
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
            ->add('nombre')
           // ->add('imagen')
            ->add('about_us',TextareaType::class,[
                   'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
           )
            ->add('address')
            ->add('email')
            ->add('phone')
            ->add('publish')
//            ->add('user')
            //->add('nombre')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('nombre')
            ->add('about_us', TemplateRegistry::TYPE_HTML, [])
            ->add('address')
            ->add('email')
            ->add('phone')
            ->add('imagen', null, array(
                'template' => 'security/_show_images.html.twig'))
        ;


    }

    public function toString($object)
    {
        return $object instanceof Cliente
            ? 'Cliente '.$object->getNombre()
            : 'Cliente'; // shown in the breadcrumb on the create view
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
    }
}
