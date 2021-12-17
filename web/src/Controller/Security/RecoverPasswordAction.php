<?php

namespace App\Controller\Security;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Security\User;
use App\Entity\Security\UserRecoverPassword;
use App\Service\Security\UserLoadService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;

class RecoverPasswordAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    /**
     * @var UserLoadService
     */
    private $userLoadService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        JWTEncoderInterface $encoder,
        UserLoadService $userLoadService,
        TranslatorInterface $translator
    )
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->userLoadService = $userLoadService;
        $this->translator = $translator;
    }

    public function __invoke(UserRecoverPassword $data, RequestStack $requestStack)
    {
        $token = $requestStack->getMasterRequest()->attributes->get('token');
        $this->validator->validate($data);
        try{
            $vars = $this->encoder->decode($token);
            $user = $this->userLoadService->getUser($vars['email']);
            if($user instanceof User){
                $this->userLoadService->recoverPasswordUser($user, $data->newPassword);
            }else{
                $data = [
                    'message' => $user,
                    'status' => 'ERROR'
                ];
                return new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        }catch (\Exception $exception){
            //return new JsonResponse(['message' => $exception->getMessage(),'status' => 'ERROR'],200);
            return new JsonResponse(['status' => 'FAILED', 'message' => $this->translator->trans($exception->getMessage(),[], 'validators')],500);
        }
        //return new JsonResponse(['status' => 'OK', 'message' => 'The password of '.$user->getUsername().' was recover']);
        return new JsonResponse(['status' => 'OK', 'message' => $this->translator->trans('user.recoverPassword.success',[],'validators')]);
    }
}
