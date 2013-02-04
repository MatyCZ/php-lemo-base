<?php

namespace LemoBase\Facade;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager
     */
    public function __construct(ServiceLocatorInterface $serviceManager)
    {
        $this->entityManager =  $serviceManager->get('Doctrine\ORM\EntityManager');
        $this->serviceManager = $serviceManager;
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
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager
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
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}
