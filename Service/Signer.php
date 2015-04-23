<?php

namespace SpomkyLabs\JoseBundle\Service;

use Jose\JWAManagerInterface;
use Jose\JWKManagerInterface;
use Jose\JWKSetManagerInterface;
use Jose\JWTManagerInterface;
use SpomkyLabs\Jose\Signer as Base;

class Signer extends Base
{
    /**
     * @var \Jose\JWAManagerInterface
     */
    protected $jwa_manager;

    /**
     * @var \Jose\JWTManagerInterface
     */
    protected $jwt_manager;

    /**
     * @var \Jose\JWKManagerInterface
     */
    protected $jwk_manager;

    /**
     * @var \Jose\JWKSetManagerInterface
     */
    protected $jwkset_manager;

    public function __construct(JWAManagerInterface $jwa_manager, JWTManagerInterface $jwt_manager, JWKManagerInterface $jwk_manager, JWKSetManagerInterface $jwkset_manager)
    {
        $this->jwa_manager         = $jwa_manager;
        $this->jwt_manager         = $jwt_manager;
        $this->jwk_manager         = $jwk_manager;
        $this->jwkset_manager      = $jwkset_manager;
    }

    /**
     * @return \Jose\JWAManagerInterface
     */
    protected function getJWAManager()
    {
        return $this->jwa_manager;
    }

    /**
     * @return \Jose\JWKManagerInterface
     */
    protected function getJWKManager()
    {
        return $this->jwk_manager;
    }

    /**
     * @return \Jose\JWKSetManagerInterface
     */
    protected function getJWKSetManager()
    {
        return $this->jwkset_manager;
    }

    /**
     * @return \Jose\JWTManagerInterface
     */
    protected function getJWTManager()
    {
        return $this->jwt_manager;
    }
}
