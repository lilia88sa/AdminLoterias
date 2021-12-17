<?php

namespace App\Repository\Core;

use App\Entity\Core\Locale;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class LocaleRepository
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var string
     */
    private $parametersDefaultLocale;
    /**
     * @var array
     */
    private $parametersAvailableLocales;

    public function __construct(
        string $parametersDefaultLocale, 
        array $parametersAvailableLocales, 
        EntityManagerInterface $entityManager
    )
    {
        $this->repository = $entityManager->getRepository(Locale::class);
        $this->parametersDefaultLocale = $parametersDefaultLocale;
        $this->parametersAvailableLocales = $parametersAvailableLocales;
    }

    /**
     * Return defaultLocale code
     * @return string
     */
    public function getDefaultLocale()
    {
        $defaultLocale = $this->parametersDefaultLocale;
        $dbDefaultLocale = $this->repository->findOneBy(array('isDefault'=>true));
        if($dbDefaultLocale){
            $defaultLocale = $dbDefaultLocale->getCode();
        }
        return $defaultLocale;
    }

    /**
     * Return array of availableLocale code
     * @return array
     */
    public function getAvailableLocales()
    {
        $qb = $this->repository->createQueryBuilder('l');
        $qb->select('l.code AS locales');
        $result = $qb->getQuery()->getResult();
        $availableLocales = array_map(function($el){ return $el['locales']; }, $result);
        if(empty($availableLocales)){
            $availableLocales = $this->parametersAvailableLocales;
        }
        return $availableLocales;
    }
}