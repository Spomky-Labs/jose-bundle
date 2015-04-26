<?php

namespace SpomkyLabs\JoseBundle\Model;

use Jose\JWTManagerInterface;

class JWTManager implements JWTManagerInterface
{
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
     * @param string $jwt_class
     * @param string $jws_class
     * @param string $jwe_class
     */
    public function __construct($jwt_class, $jws_class, $jwe_class)
    {
        $this->jwt_class = $jwt_class;
        $this->jws_class = $jws_class;
        $this->jwe_class = $jwe_class;
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
