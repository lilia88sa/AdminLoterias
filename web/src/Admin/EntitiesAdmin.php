<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Entities;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class EntitiesAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('comercialName')
            ->add('name')
            ->add('code')
            ->add('schedule')
            ->add('schedulePublic')
            ->add('phone')
            ->add('email')
            ->add('website')
            ->add('entityType')
            ->add('publish')
            ->add('createdAt')
            ->add('updatedAt')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('comercialName')
            ->add('user')
            ->add('name')
            ->add('entityType')
            ->add('schedule')
            ->add('phone')
            ->add('email')
            ->add('website')
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

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $securityContext = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');
       if($securityContext->isGranted('ROLE_EDITOR') ||
            $securityContext->isGranted('ROLE_SUPER_ADMIN') ||
            $securityContext->isGranted('ROLE_ADMIN') ){
            $formMapper
                ->add('comercialName')
                ->add('name')
                ->add('code')
                ->add('socialReason',null,['attr' => ['rows' => 10]])
                ->add('schedule')
                ->add('schedulePublic')
                ->add('postalCode')
                ->add('phone')
                ->add('email')
                ->add('website',UrlType::class,[
                    'required' => false
                ])
                ->add('description',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->add('serviceDescription',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->add('entityType', ChoiceType::class,[
                    'choices' => [
                        'ESTATAL' => Entities::ENTITY_TYPE_ESTATAL,
                        'PARTICULAR' => Entities::ENTITY_TYPE_PARTICULAR,
                    ]
                ])
                ->add('user')
            ;
        }elseif($securityContext->isGranted('ROLE_USER')){
            $formMapper
                ->add('comercialName')
                ->add('name')
                ->add('code')
                ->add('socialReason',null,['attr' => ['rows' => 10]])
                ->add('schedule')
                ->add('schedulePublic')
                ->add('postalCode')
                ->add('phone')
                ->add('email')
                ->add('website',UrlType::class,[
                    'required' => false
                ])
                ->add('description',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->add('serviceDescription',TextareaType::class,[
                        'attr' => array('class' => 'ckeditor', 'name' => 'editor')]
                )
                ->add('entityType')
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
            ->add('comercialName')
            ->add('name')
            ->add('code')
            ->add('socialReason')
            ->add('schedule')
            ->add('schedulePublic')
            ->add('postalCode')
            ->add('phone')
            ->add('email')
            ->add('website')
            ->add('description')
            ->add('serviceDescription')
            ->add('entityType')
            ->add('publish')
            ->add('createdAt')
            ->add('entitiesFiles', null, array(
                'template' => 'security/_show_images.html.twig'))
            ;
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $query = parent::configureQuery($query);

        $securityContext = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');
        if($securityContext->isGranted('ROLE_EDITOR') ||
            $securityContext->isGranted('ROLE_SUPER_ADMIN') ||
            $securityContext->isGranted('ROLE_ADMIN') ){
        }elseif($securityContext->isGranted('ROLE_USER')){

            $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
            $rootAlias = current($query->getRootAliases());
            $query->andWhere(
                $query->expr()->eq($rootAlias . '.user', ':user')
            );
            $query->setParameter('user', $user);
        }
        return $query;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('entityApproval',
            $this->getRouterIdParameter().'/entity-approval');

        $collection->add('entityRejected',
            $this->getRouterIdParameter().'/entity-rejected');
    }

    public function toString($object)
    {
        return $object instanceof Entities
            ? 'Entidad '.$object->getComercialName()
            : 'Entidad'; // shown in the breadcrumb on the create view
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
    }
}
