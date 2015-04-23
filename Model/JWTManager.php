<?php

namespace SpomkyLabs\JoseBundle\Model;

use Doctrine\Common\Persistence\ManagerRegistry;
use Jose\JWTManagerInterface;

class JWTManager implements JWTManagerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|null
     */
    private $jwt_entity_repository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|null
     */
    private $jws_entity_repository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|null
     */
    private $jwe_entity_repository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|null
     */
    private $jwt_entity_manager = null;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|null
     */
    private $jws_entity_manager = null;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager|null
     */
    private $jwe_entity_manager = null;

    /**
     * @var string
     */
    private $jwt_class;

    /**
     * @var string
     */
    private $jws_class;

    /**
     * @var string
     */
    private $jwe_class;

    /**
     * @param string                                       $jwt_class
     * @param string                                       $jws_class
     * @param string                                       $jwe_class
     * @param \Doctrine\Common\Persistence\ManagerRegistry $manager_registry
     */
    public function __construct($jwt_class, $jws_class, $jwe_class, ManagerRegistry $manager_registry = null)
    {
        $this->jwt_class = $jwt_class;
        $this->jws_class = $jws_class;
        $this->jwe_class = $jwe_class;

        if (!is_null($manager_registry)) {
            $this->jwt_entity_manager = $manager_registry->getManagerForClass($jwt_class);
            $this->jws_entity_manager = $manager_registry->getManagerForClass($jws_class);
            $this->jwe_entity_manager = $manager_registry->getManagerForClass($jwe_class);

            $this->jwt_entity_repository = $this->entity_manager->getRepository($jwt_class);
            $this->jws_entity_repository = $this->entity_manager->getRepository($jws_class);
            $this->jwe_entity_repository = $this->entity_manager->getRepository($jwe_class);
        }
    }

    /**
     * @return string
     */
    protected function getJWTClass()
    {
        return $this->jwt_class;
    }

    /**
     * @return string
     */
    protected function getJWSClass()
    {
        return $this->jws_class;
    }

    /**
     * @return string
     */
    protected function getJWEClass()
    {
        return $this->jwe_class;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|null
     */
    public function getJWTEntityRepository()
    {
        return $this->jwt_entity_repository;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|null
     */
    public function getJWSEntityRepository()
    {
        return $this->jws_entity_repository;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|null
     */
    public function getJWEEntityRepository()
    {
        return $this->jwe_entity_repository;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    public function getJWTEntityManager()
    {
        return $this->jwt_entity_manager;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    public function getJWSEntityManager()
    {
        return $this->jws_entity_manager;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    public function getJWEEntityManager()
    {
        return $this->jwe_entity_manager;
    }

    /**
     * Create an empty JWT object.
     *
     * @return \Jose\JWTInterface
     */
    public function createJWT()
    {
        $class = $this->getJWTClass();
        $jwt = new $class();

        return $jwt;
    }

    /**
     * Create an empty JWS object.
     *
     * @return \Jose\JWSInterface
     */
    public function createJWS()
    {
        $class = $this->getJWSClass();
        $jws = new $class();

        return $jws;
    }

    /**
     * Create an empty JWE object.
     *
     * @return \Jose\JWEInterface
     */
    public function createJWE()
    {
        $class = $this->getJWEClass();
        $jwe = new $class();

        return $jwe;
    }
}
