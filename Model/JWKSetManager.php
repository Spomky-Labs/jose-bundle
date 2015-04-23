<?php

namespace SpomkyLabs\JoseBundle\Model;

use Doctrine\Common\Persistence\ManagerRegistry;
use Jose\JWKManagerInterface;
use Jose\JWKSetInterface;
use Jose\JWKSetManager as Base;

class JWKSetManager extends Base
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
     * @var \Jose\JWKManagerInterface
     */
    private $jwk_manager;

    /**
     * @param \Jose\JWKManagerInterface                    $jwk_manager
     * @param string                                       $class
     * @param \Doctrine\Common\Persistence\ManagerRegistry $manager_registry
     */
    public function __construct(JWKManagerInterface $jwk_manager, $class, ManagerRegistry $manager_registry = null)
    {
        $this->class = $class;
        $this->jwk_manager = $jwk_manager;
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
     * @param \Jose\JWKSetInterface $jwk_set
     *
     * @return $this
     */
    public function save(JWKSetInterface $jwk_set)
    {
        if (is_null($this->getEntityManager())) {
            throw new \RuntimeException('Doctrine not available');
        } else {
            $this->getEntityManager()->persist($jwk_set);
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
    public function createJWKSet(array $values = array())
    {
        $class = $this->getClass();
        /*
         *
         * @var \Jose\JWKSetInterface $jwk_set
         */
        $jwk_set = new $class();
        $jwk_set->setValues($values);

        return $jwk_set;
    }

    protected function getJWKManager()
    {
        return $this->jwk_manager;
    }
}
