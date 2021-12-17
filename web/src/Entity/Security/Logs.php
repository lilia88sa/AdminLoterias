<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 01/07/2020
 * Time: 12:12
 */

namespace App\Entity\Security;


use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Gedmo\Blameable\Traits\BlameableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ApiResource()
 */
class Logs extends AbstractLogEntry
{
    /**
     * Hook blameable behavior
     * updates createdBy, updatedBy fields
     */
    use BlameableEntity;

    /**
     * @Groups({"get-admin"})
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    /**
     * @Groups({"get-admin"})
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @Groups({"get-admin"})
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @Groups({"get-admin"})
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @Groups({"get-admin"})
     */
    public function getLoggedAt()
    {
        return $this->loggedAt;
    }

    /**
     * @Groups({"get-admin"})
     */
    public function getData()
    {
        return $this->data;
    }

}