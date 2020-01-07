<?php

namespace LemoBase\Facade;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Laminas\ServiceManager\ServiceLocatorInterface;

abstract class AbstractFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Laminas\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $serviceManager
     */
    public function __construct(ServiceLocatorInterface $serviceManager)
    {
        $this->entityManager =  $serviceManager->get('Doctrine\ORM\EntityManager');
        $this->serviceManager = $serviceManager;
    }

    /**
     * Prevede entitu na pole
     *
     * @param object $entity
     * @return array
     */
    public function extract($entity)
    {
        $hydrator = new DoctrineHydrator($this->getEntityManager(), get_class($entity));

        return $hydrator->extract($entity);
    }

    /**
     * Prevede pole na entitu
     *
     * @param  object $entity
     * @param  array  $array
     * @return object
     */
    public function hydrate($entity, array $array)
    {
        $hydrator = new DoctrineHydrator($this->getEntityManager(), get_class($entity));

        return $hydrator->hydrate($array, $entity);
    }

    /**
     * Set entityManager
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return AbstractFacade;
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Get entityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Set service manager instance
     *
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $serviceManager
     * @return AbstractFacade
     */
    public function setServiceManager(ServiceLocatorInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return \Laminas\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}
