<?php


namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 */
class UserRecoverPassword
{
    /**
     * @ApiProperty(identifier=true)
     */
    public $token;

    /**
     * @Assert\NotNull(groups={"user:get:recover:password"})
     * @Assert\Length(
     *     min=8
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$&*])/",
     *     message="user.password.complex"
     * )
     * @Groups({"user:get:recover:password"})
     */
    public $newPassword;

    /**
     * @Assert\NotBlank(groups={"user:get:recover:password"})
     * @Assert\Expression(
     *     "this.getNewPassword() === this.getRepeatPassword()",
     *     message="user.repeatPassword.mo_match"
     * )
     * @Groups({"user:get:recover:password"})
     */
    public $repeatPassword;

    /**
     * @Assert\Email(groups={"user:get:recover"})
     */
    public $email;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return mixed
     */
    public function getRepeatPassword()
    {
        return $this->repeatPassword;
    }

    /**
     * @param mixed $repeatPassword
     */
    public function setRepeatPassword($repeatPassword)
    {
        $this->repeatPassword = $repeatPassword;
    }

}
