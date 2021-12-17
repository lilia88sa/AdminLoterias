<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 2/20/2021
 * Time: 8:31 PM
 */

namespace App\Service\Security;


use App\Entity\Entities;
use App\Repository\EntitiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class StatusUpdateService
{

    /**
     * @var EntitiesRepository
     */
    private $entitiesRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        EntitiesRepository $entitiesRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->entitiesRepository = $entitiesRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function updateEntityStatus(int $entityId, $entityType)
    {
        $entity = null;
        $this->logger->debug('Fetching entity by entity ID');
        if($entityType == 'ENTITY'){
            $entity = $this->entitiesRepository->findOneById($entityId);
        }

        // entity was NOT found by ID
        if (is_null($entity)) {
            $this->logger->debug('entity by ID not found');
            $error = 'Entidad No encontrada';
            throw new EntityNotFoundException($error);
        }

        if($entity->getPublish()){
            $entity->setPublish(false);
            $this->entityManager->persist($entity);
            $this->logger->debug('Entity Locked status');
        }else{
            $entity->setPublish(true);
            $this->logger->debug('Entity publish Status');
        }
        $this->entityManager->flush();

    }
}