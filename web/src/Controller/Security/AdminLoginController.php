<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 07/09/2020
 * Time: 02:19
 */

namespace App\Controller\Security;

use App\Entity\Security\User;
use App\Form\AdminRegisterType;
use App\Service\Core\Mailer;
use App\Service\Security\TokenGenerator;
use App\Service\Security\UserConfirmationService;
use App\Service\Security\UserLoadService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\AdminLoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;


final class AdminLoginController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/admin/login", name="admin_login")
     */
    public function loginAction(): Response
    {
        $form = $this->createForm(AdminLoginType::class, [
            'email' => $this->authenticationUtils->getLastUsername()
        ]);

        return $this->render('security/login.html.twig', [
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'form' => $form->createView(),
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/admin/registro", name="admin_registro")
     */
    public function registroAction(Request $request,
                                   UserPasswordEncoderInterface $encoder,
                                   TokenGenerator $tokenGenerator,
                                   Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(AdminRegisterType::class, $user, [
            'action_type' => 'register'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $in_use = $em->getRepository(User::class)->findOneByEmail($user->getEmail());
            if(!is_null($in_use)){
                $this->get('session')->getFlashBag()->set('error', array(
                    'message' => 'Correo en uso'));
                return $this->redirectToRoute('admin_login');
            }

            $user->setPassword(
                $encoder->encodePassword(
                    $user, $user->getPassword()));
            $user->setConfirmationToken(
                $tokenGenerator->getRandomSecureToken($user->getEmail()));

            $em->persist($user);
            $em->flush();

            $mailer->sendConfirmationEmail($user);

            $this->get('session')->getFlashBag()->set('success', array(
                'message' => 'Su perfil ha sido creado correctamente. Se ha enviado un correo para validar el mismo'));
            return $this->redirectToRoute('admin_login');
        }
        return $this->render('security/registro.html.twig', [
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'form' => $form->createView(),
            'action_type' => 'register',
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/admin/account-confirm/{token}", name="admin_account_confirm")
     */
    public function confirmAccountAction(Request $request, UserConfirmationService $confirmationService)
    {
        $error = $confirmationService->confirmUser($request->get('token'));

        if (!is_null($error) && is_array($error)){
            $this->get('session')->getFlashBag()->set('error', array(
                'message' => 'Su validación ha expirado, un nuevo token de validacion ha sido enviado a su correo para su confirmación'));
            return $this->redirectToRoute('admin_login');
        }elseif(!is_null($error) && !is_array($error)){
            $this->get('session')->getFlashBag()->set('error', array(
                'message' => $error));
            return $this->redirectToRoute('admin_login');
        }else{
            $this->get('session')->getFlashBag()->set('success', array(
                'message' => 'Su perfil ha sido validado. Para acceder a los servicio un administrador debe habilitar su uso'));
            return $this->redirectToRoute('admin_login');
        }
    }

    /**
     * @Route("/admin/recover-password-email", name="admin_recover_password_email")
     */
    public function recoverPasswordEmailAction(Request $request,
                                               TokenGenerator $tokenGenerator,
                                               Mailer $mailer,
                                               UserLoadService $userLoadService): Response
    {
        $user = new User();
        $form = $this->createForm(AdminRegisterType::class, $user, [
            'action_type' => 'recover_password_email'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userLoadService->getUserByEmail($user->getEmail());
            if($user instanceof User){
                $token = $tokenGenerator->getOneHourSecureToken($user->getEmail());
                $mailer->sendRecoverPasswordEmail($user, $token);
                $this->get('session')->getFlashBag()->set('success', array(
                    'message' => 'Se ha enviado un enlace a su correo para restablecer su contraseña'));
                return $this->redirectToRoute('admin_login');

            }else{
                $this->get('session')->getFlashBag()->set('error', array(
                    'message' => 'El correo es invalido'));
                return $this->redirectToRoute('admin_login');
            }
        }
        return $this->render('security/registro.html.twig', [
            'form' => $form->createView(),
            'action_type' => 'recover_password_email',
        ]);
    }

    /**
     * @Route("/admin/new-password/{token}", name="admin_recover_password_password")
     */
    public function recoverPasswordPasswordAction(Request $request,
                                                  UserLoadService $userLoadService,
                                                  JWTEncoderInterface $encoder)
    {
          if($request->getMethod() == "GET"){
              $token = $request->get('token');
              $vars = $encoder->decode($token);
              $user = $userLoadService->getUser($vars['email']);
              if($user instanceof User){
                  $user_validation = new User();
                  $form = $this->createForm(AdminRegisterType::class, $user_validation, [
                      'action_type' => 'recover_password_password'
                  ]);
                  //$userLoadService->recoverPasswordUser($user, $data->newPassword);
                  return $this->render('security/registro.html.twig', [
                      'form' => $form->createView(),
                      'user_id' => $user->getId(),
                      'action_type' => 'recover_password_password',
                  ]);
              }else{
                  $this->get('session')->getFlashBag()->set('error', array(
                      'message' => 'El token es invalido'));
                  return $this->redirectToRoute('admin_login');
              }
          }elseif($request->getMethod() == "POST"){
              $user = new User();
              $id = $request->get('id');
              $user_to_update = $this->getDoctrine()->getRepository(User::class)->findOneById($id);
              $form = $this->createForm(AdminRegisterType::class, $user, [
                  'action_type' => 'recover_password_password'
              ]);
              $form->handleRequest($request);
              if ($form->isSubmitted()) {
                  $userLoadService->recoverPasswordUser($user_to_update, $user->getPassword());
                  $this->get('session')->getFlashBag()->set('success', array(
                      'message' => 'Su contraseña ha sido cambiada'));
                  return $this->redirectToRoute('admin_login');
              }
              return $this->render('security/registro.html.twig', [
                  'form' => $form->createView(),
                  'action_type' => 'recover_password_password',
              ]);
          }
    }

    /**
     * @Route("/admin/logout", name="admin_logout")
     */
    public function logoutAction(): void
    {
        // Left empty intentionally because this will be handled by Symfony.
    }
}