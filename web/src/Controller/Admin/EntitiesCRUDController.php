<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Security\User;
use App\Repository\EntitiesRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use App\Service\Core\Mailer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

final class EntitiesCRUDController extends CRUDController
{
    /**
     * @var Mailer
     */
    private $mailer;

    private $entitiesRepository;

    public function __construct(
        Mailer $mailer,
       EntitiesRepository $entitiesRepository
    )
    {
        $this->mailer = $mailer;
        $this->entitiesRepository = $entitiesRepository;
    }
    /** APROBAR O RECHAZADO ENTIDAD */
    public function entityApprovalAction(){
        $entity = $this->admin->getSubject();
        $updateService = $this->container->get('security.status.update');
        $updateService->updateEntityStatus($entity->getId(), 'ENTITY');

        $mailer = $this->container->get('app.mailer');
        $email = $entity->getEmail();
        $mailer->sendStatusEntityEmail($email,
            $entity->getPublish() ? 'Su Entidad '.$entity->getName().' ha sido aprobada' : 'Su Entidad '.$entity->getName().' no ha sido aprobada',
            $entity->getPublish() ? 'ACCEPTED' : 'BLOCKED');


        $this->addFlash('sonata_flash_success', 'Entidad aprobada se ha enviado un
          Email enviado notificando a
          '.$email);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    public function entityRejectedAction(RequestStack $requestStack, $id){
        $entity = $this->entitiesRepository->findOneById($id);
        if($requestStack->getCurrentRequest()->getMethod() == "POST"){
            $updateService = $this->container->get('security.status.update');
            if($entity->getPublish()){
                $updateService->updateEntityStatus($entity->getId(), 'ENTITY');
            }

            $email = $entity->getEmail();
            $mailer = $this->container->get('app.mailer');
            $mailer->sendInvalidationEmail($entity->getEmail(),
                $requestStack->getCurrentRequest()->get('rejected'),
                $entity->getName());
            $this->addFlash('sonata_flash_success', 'Entidad no aprobada ,se ha enviado un
          Email notificando a
          '.$email);
            return $this->redirectTo($entity) ;
        }
        return  $this->render('security/reject_user.html.twig',
            array(
                'user' => $entity,
                'userRejected' => 'entityRejected',
                'object' => $this->admin->getSubject(),
                'admin' => $this->admin,
                'action' => 'list',
                'edit' => 'show'
            ));
    }

    /**
     * Override the default editAction to only allow a General Manager to modify it's hotel
     *
     * @param $id
     * @return Response
     */
    function editAction($id = null)
    {
        $notUserOwnerfind = true;
        $user = $this->getUser();
        if(in_array(User::ROLE_ADMIN, $user->getRoles()) ||
            in_array(User::ROLE_SUPER_ADMIN, $user->getRoles()) ||
            in_array(User::ROLE_EDITOR, $user->getRoles())){
            return parent::editAction($id);
        }else{
            // We assume here that the user has a function that return the Hotel he is managing
            $entities = $user->getEntities();
            foreach ( $entities as $entity) {
                if (in_array(User::ROLE_USER, $user->getRoles())
                    && $id == $entity->getId()) {
                    $notUserOwnerfind = false;
                }
            }
            if($notUserOwnerfind){
                throw new AccessDeniedException();
            }else{
                return parent::editAction($id);
            }
        }
    }
}
