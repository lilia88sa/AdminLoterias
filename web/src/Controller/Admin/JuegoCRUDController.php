<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Juego;
use App\Repository\JuegoRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use App\Service\Core\Mailer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

final class JuegoCRUDController extends CRUDController
{
    /**
     * @var Mailer
     */
    private $mailer;

    private $juegoRepository;

    public function __construct(
        Mailer $mailer,
       JuegoRepository $juegoRepository
    )
    {
        $this->mailer = $mailer;
        $this->juegoRepository = $juegoRepository;
    }
    /** APROBAR O RECHAZADO ENTIDAD */
    public function juegoApprovalAction(){
        $juego = $this->admin->getSubject();
        $updateService = $this->container->get('security.status.update');
        $updateService->updateEntityStatus($juego->getId(), 'ENTITY');

        $mailer = $this->container->get('app.mailer');
        $email = $juego->getEmail();
        $mailer->sendStatusEntityEmail($email,
            $juego->getPublish() ? 'Su Entidad '.$juego->getName().' ha sido aprobada' : 'Su Entidad '.$juego->getNombre().' no ha sido aprobada',
            $juego->getPublish() ? 'ACCEPTED' : 'BLOCKED');


        $this->addFlash('sonata_flash_success', 'Entidad aprobada se ha enviado un
          Email enviado notificando a
          '.$email);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    public function juegoRejectedAction(RequestStack $requestStack, $id){
        $juego = $this->juegoRepository->findOneById($id);
        if($requestStack->getCurrentRequest()->getMethod() == "POST"){
            $updateService = $this->container->get('security.status.update');
            if($juego->getPublish()){
                $updateService->updateEntityStatus($juego->getId(), 'ENTITY');
            }

            $email = $juego->getEmail();
            $mailer = $this->container->get('app.mailer');
            $mailer->sendInvalidationEmail($juego->getEmail(),
                $requestStack->getCurrentRequest()->get('rejected'),
                $juego->getNombre());
            $this->addFlash('sonata_flash_success', 'Entidad no aprobada ,se ha enviado un
          Email notificando a
          '.$email);
            return $this->redirectTo($juego) ;
        }
        return  $this->render('security/reject_user.html.twig',
            array(
                'juego' => $juego,
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
