<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Model;

use Doctrine\Common\Persistence\ManagerRegistry;

class JotManager implements JotManagerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $manager_registry = null;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|null
     */
    private $entity_repository = null;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|null
     */
    private $entity_manager = null;

    /**
     * @var string
     */
    private $class;

    /**
     * @param                                              $class
     * @param \Doctrine\Common\Persistence\ManagerRegistry $manager_registry
     */
    public function __construct($class, ManagerRegistry $manager_registry)
    {
        $this->setClass($class);
        $this->setManagerRegistry($manager_registry);
    }

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $manager_registry
     *
     * @return $this
     */
    private function setManagerRegistry(ManagerRegistry $manager_registry)
    {
        $this->manager_registry = $manager_registry;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Persistence\ManagerRegistry
     */
    private function getManagerRegistry()
    {
        return $this->manager_registry;
    }

    /**
     * @param string $class
     *
     * @return self
     */
    private function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    private function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager()
    {
        if (null === $this->entity_manager) {
            $this->entity_manager = $this->getManagerRegistry()->getManagerForClass($this->getClass());
        }

        return $this->entity_manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityRepository()
    {
        if (null === $this->entity_repository) {
            $this->entity_repository = $this->getEntityManager()->getRepository($this->getClass());
        }

        return $this->entity_repository;
    }

    /**
     * {@inheritdoc}
     */
    public function createJot()
    {
        $class = $this->getClass();

        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function saveJot(JotInterface $jot)
    {
        $this->getEntityManager()->persist($jot);
        $this->getEntityManager()->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeJot(JotInterface $jot)
    {
        $this->getEntityManager()->remove($jot);
        $this->getEntityManager()->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJotById($jti)
    {
        return $this->getEntityRepository()->findOneBy(['jti' => $jti]);
    }
}
