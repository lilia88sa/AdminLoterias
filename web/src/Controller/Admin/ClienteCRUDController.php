<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Security\User;
use App\Repository\ClienteRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use App\Service\Core\Mailer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

final class ClienteCRUDController extends CRUDController
{
    /**
     * @var Mailer
     */
    private $mailer;

    private $clienteRepository;

    public function __construct(
        Mailer $mailer,
       ClienteRepository $clienteRepository
    )
    {
        $this->mailer = $mailer;
        $this->clienteRepository = $clienteRepository;
    }
    /** APROBAR O RECHAZADO ENTIDAD */
    public function clienteApprovalAction(){
        $cliente = $this->admin->getSubject();
        $updateService = $this->container->get('security.status.update');
        $updateService->updateEntityStatus($cliente->getId(), 'ENTITY');

        $mailer = $this->container->get('app.mailer');
        $email = $cliente->getEmail();
        $mailer->sendStatusEntityEmail($email,
            $cliente->getPublish() ? 'Su Entidad '.$cliente->getName().' ha sido aprobada' : 'Su Entidad '.$cliente->getNombre().' no ha sido aprobada',
            $cliente->getPublish() ? 'ACCEPTED' : 'BLOCKED');


        $this->addFlash('sonata_flash_success', 'Entidad aprobada se ha enviado un
          Email enviado notificando a
          '.$email);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    public function clienteRejectedAction(RequestStack $requestStack, $id){
        $cliente = $this->clienteRepository->findOneById($id);
        if($requestStack->getCurrentRequest()->getMethod() == "POST"){
            $updateService = $this->container->get('security.status.update');
            if($cliente->getPublish()){
                $updateService->updateEntityStatus($cliente->getId(), 'ENTITY');
            }

            $email = $cliente->getEmail();
            $mailer = $this->container->get('app.mailer');
            $mailer->sendInvalidationEmail($cliente->getEmail(),
                $requestStack->getCurrentRequest()->get('rejected'),
                $cliente->getNombre());
            $this->addFlash('sonata_flash_success', 'Entidad no aprobada ,se ha enviado un
          Email notificando a
          '.$email);
            return $this->redirectTo($cliente) ;
        }
        return  $this->render('security/reject_user.html.twig',
            array(
                'cliente' => $cliente,
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
            $clientes = $user->getCliente();
            foreach ( $clientes as $cliente) {
                if (in_array(User::ROLE_USER, $user->getRoles())
                    && $id == $cliente->getId()) {
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
