<?php

namespace SpomkyLabs\JoseBundle\Model;

use Doctrine\Common\Persistence\ManagerRegistry;
use Jose\JWKInterface;
use Jose\JWKManager as Base;

class JWKManager extends Base
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|null
     */
    private $entity_repository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|null
     */
    private $entity_manager = null;

    /**
     * @var string
     */
    private $class;

    /**
     * @param string                                       $class
     * @param \Doctrine\Common\Persistence\ManagerRegistry $manager_registry
     */
    public function __construct($class, ManagerRegistry $manager_registry = null)
    {
        $this->class = $class;
        if (!is_null($manager_registry)) {
            $this->entity_manager = $manager_registry->getManagerForClass($class);
            $this->entity_repository = $this->entity_manager->getRepository($class);
        }
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return $this->class;
    }

    /**
     * @param \Jose\JWKInterface $jwk
     *
     * @return $this
     */
    public function save(JWKInterface $jwk)
    {
        if (is_null($this->getEntityManager())) {
            throw new \RuntimeException('Doctrine not available');
        } else {
            $this->getEntityManager()->persist($jwk);
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|null
     */
    public function getEntityRepository()
    {
        return $this->entity_repository;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    public function getEntityManager()
    {
        return $this->entity_manager;
    }

    /**
     * Create a JWK object.
     *
     * @param array $values The values to set.
     *
     * @return \Jose\JWKInterface Returns a JWKInterface object
     */
    public function createJWK(array $values = array())
    {
        $class = $this->getClass();
        $jwk = new $class();
        $jwk->setValues($values);

        return $jwk;
    }
}
