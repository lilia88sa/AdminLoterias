<?php

namespace App\Service\Core;

use App\Entity\Security\User;
use Swift_Image;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Symfony\Contracts\Translation\TranslatorInterface;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        \Swift_Mailer $mailer,
        Environment $twig,
        TranslatorInterface $translator,
        ParameterBagInterface $parameterBag
    )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->parameterBag = $parameterBag;
    }

    public function sendNotificationEmail(User $user, $description){
        $message = (new Swift_Message($this->translator->trans("notification.email",[],'validators')))
            ->setFrom('no-reply@infocap.cu')
            ->setTo($user->getEmail());

        $logo = $message->embed(Swift_Image::fromPath(('templates_assets/img/logo_horizone.png')));
        $image = $message->embed(Swift_Image::fromPath(('templates_assets/img/img_welcome@2x.png')));

        $body = $this->twig->render(
            'email/notifications/notifications.html.twig',
            [
                'user' => $user,
                'logo' => $logo,
                'image' => $image,
                'description' => $description
            ]
        );

        $message->setBody($body, 'text/html');
        $this->mailer->send($message);
    }

    public function sendConfirmationEmail(User $user)
    {
        $message = (new Swift_Message($this->translator->trans("user.login.confirmation",[],'validators')))
            ->setFrom('no-reply@infocap.cu')
            ->setTo($user->getEmail());

        $logo = $message->embed(Swift_Image::fromPath(('templates_assets/img/logo_dasboard.png')));
        $image = $message->embed(Swift_Image::fromPath(('templates_assets/img/bienvenido.png')));
        $btn = $message->embed(Swift_Image::fromPath(('templates_assets/img/confirmar.png')));
        
        $preRegister = $this->parameterBag->get('pre_register');
        
        $body = $this->twig->render(
            'email/security/account-welcome.html.twig',
            [
                'user' => $user,
                'logo' => $logo,
                'image' => $image,
                'btn' => $btn,
                'preRegister' => $preRegister
            ]
        );

        $message->setBody($body, 'text/html');
        $this->mailer->send($message);
    }

    public function sendRecoverPasswordEmail(User $user, $token)
    {
        $message = (new Swift_Message($this->translator->trans("user.login.recoverconfirmation",[],'validators')))
            ->setFrom('no-reply@infocap.cu')
            ->setTo($user->getEmail());

        $logo = $message->embed(Swift_Image::fromPath(('templates_assets/img/logo_dasboard.png')));
        $restablecerPassword = $message->embed(Swift_Image::fromPath(('templates_assets/img/img_restorepass.png')));
        $btnReset = $message->embed(Swift_Image::fromPath(('templates_assets/img/btn-restorepass.png')));

        $body = $this->twig->render(
            'email/security/account-restorepassword.html.twig',
            [
                'user' => $user,
                'token' => $token,
                'logo' => $logo,
                'restablecerPassword' => $restablecerPassword,
                'btnReset' => $btnReset
            ]
        );

        $message->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendStatusEntityEmail($email, $description, $type){
        if($type == 'BLOCKED'){
            $subject = 'Su Entidad se ha rechazado';
        }else{
            $subject = 'Su Entidad ha sido aprobada';
        }
        $message = (new Swift_Message($subject))
            ->setFrom('no-reply@infocap.cu')
            ->setTo($email);

        $logo = $message->embed(Swift_Image::fromPath(('templates_assets/img/logo_dasboard.png')));
        $image = $message->embed(Swift_Image::fromPath(('templates_assets/img/bienvenido.png')));

        $body = $this->twig->render(
            'email/notifications/notifications.html.twig',
            [
                'email' => $email,
                'logo' => $logo,
                'image' => $image,
                'description' => $description
            ]
        );

        $message->setBody($body, 'text/html');
        $this->mailer->send($message);
    }

    public function sendInvalidationEmail($email, $textMessage, $name = '')
    {
        $message = (new Swift_Message('Entidad Invalidada'))
            ->setFrom('no-reply@infocap.cu')
            ->setTo($email);

        $logo = $message->embed(Swift_Image::fromPath(('templates_assets/img/logo_dasboard.png')));


        $body = $this->twig->render(
            'email/security/not-validated.html.twig',
            [
                'textMessage' => $textMessage,
                'logo' => $logo,
                'name' => $name
            ]
        );

        $message->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendUpdateTranslationStadisticsEmail($stadistics){
        $message = (new Swift_Message('traducciones del mes'))
            ->setFrom('no-reply@infocap.cu')
            ->setTo('pviltres@infocap.cu'/*$this->parameterBag->get('email_youtube_update')*/);

        $body = $this->twig->render(
            'email/security/stats-translations.html.twig', [
                'stadistics' => $stadistics
            ]
        );

        $message->setBody($body, 'text/html');
        $this->mailer->send($message);
    }
}
