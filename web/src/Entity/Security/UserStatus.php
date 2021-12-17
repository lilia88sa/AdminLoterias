<?php


namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 */
class UserStatus
{
    /**
     * @Assert\NotBlank()
     */
    public $status;

    /**
     * @Assert\NotBlank()
     */
    public $user;

}
