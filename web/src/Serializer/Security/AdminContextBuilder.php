<?php
// src/Serializer/Security/AdminContextBuilder.php
namespace App\Serializer\Security;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Security\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AdminContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest(
            $request,
            $normalization,
            $extractedAttributes
        );

        if (
            isset($context['groups']) &&
            $normalization === true &&
            ($this->authorizationChecker->isGranted(User::ROLE_ADMIN) || 
            $this->authorizationChecker->isGranted(User::ROLE_SUPER_ADMIN))
        ) {
            $context['groups'][] = 'get-admin';
        }

        return $context;
    }
}
