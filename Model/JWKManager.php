<?php

namespace SpomkyLabs\JoseBundle\Model;

use Jose\JWKManager as Base;
use SpomkyLabs\Jose\Util\ECConverter;
use SpomkyLabs\Jose\Util\RSAConverter;

class JWKManager extends Base implements JWKManagerInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return $this->class;
    }

    /**
     * @param array $values
     *
     * @return \SpomkyLabs\JoseBundle\Model\JWKInterface
     */
    public function createJWK(array $values = array())
    {
        $class = $this->getClass();
        /**
         * @var \SpomkyLabs\JoseBundle\Model\JWKInterface
         */
        $jwk = new $class();
        $jwk->setValues($values);

        return $jwk;
    }

    /**
     * @inheritdoc()
     */
    public function loadKeyFromX509Certificate($certificate, $passphrase = null)
    {
        $values = RSAConverter::loadKeyFromFile($certificate, $passphrase);

        return $this->createJWK($values);
    }

    /**
     * @inheritdoc()
     */
    public function loadKeyFromECCCertificate($certificate)
    {
        $values = ECConverter::loadKeyFromFile($certificate);

        return $this->createJWK($values);
    }

    /**
     * @inheritdoc()
     */
    public function loadKeyFromValues(array $values)
    {
        return $this->createJWK($values);
    }

    public function findKeyById($kid, $public)
    {
    }
}
