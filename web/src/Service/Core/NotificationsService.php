<?php


namespace App\Service\Core;


use App\Entity\Core\Notifications;
use App\Entity\Core\NotificationsTranslation;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Core\Mailer;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationsService
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Mailer $mailer,
        TranslatorInterface $translator
    )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    public function createNotificationRequest(string $description,
                                       string $messageType,
                                       string $notificationsType,
                                       int $user_id,
                                       bool $name = false,
                                       int  $user_id_request){
        $user = $this->userRepository->findOneById($user_id);
        $user_request = $this->userRepository->findOneById($user_id_request);

        $notification = new Notifications();
        $notification->setUser($user);
        $notification->setDescription($description);
        $notification->setMessageType($messageType);
        $notification->setNotificationsType($notificationsType);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
        if($name){
            $notification_msg_es = $this->translator->trans($description,['%name%' => $user_request->getName()],'notifications', 'es');
            $notification_msg_en = $this->translator->trans($description,['%name%' => $user_request->getName()],'notifications', 'en');
        }else{
            $notification_msg_es = $this->translator->trans($description,[],'notifications', 'es');
            $notification_msg_en = $this->translator->trans($description,[],'notifications', 'en');
        }
        $notification_es = new NotificationsTranslation();
        $notification_es->setLocale('es');
        $notification_es->setObjectClass(Notifications::class);
        $notification_es->setField('description');
        $notification_es->setContent($notification_msg_es);
        $notification_es->setForeignKey($notification->getId());
        $this->entityManager->persist($notification_es);

        $notification_en = new NotificationsTranslation();
        $notification_en->setLocale('en');
        $notification_en->setObjectClass(Notifications::class);
        $notification_en->setField('description');
        $notification_en->setContent($notification_msg_en);
        $notification_en->setForeignKey($notification->getId());
        $this->entityManager->persist($notification_en);
        $this->entityManager->flush();

        //SEND NOTIFICATION EMAIL
        $this->mailer->sendNotificationEmail($user, $notification_msg_es);
    }
}
