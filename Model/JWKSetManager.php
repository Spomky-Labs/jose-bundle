<?php

namespace SpomkyLabs\JoseBundle\Model;

use Jose\JWKSetManager as Base;

class JWKSetManager extends Base implements JWKSetManagerInterface
{
    /**
     * @var \SpomkyLabs\JoseBundle\Model\JWKSetInterface
     */
    private $private_jwkset;

    /**
     * @var \SpomkyLabs\JoseBundle\Model\JWKSetInterface
     */
    private $public_jwkset = null;

    /**
     * @var string
     */
    private $class;

    /**
     * @var \Jose\JWKManagerInterface
     */
    private $jwk_manager;

    /**
     * @param \SpomkyLabs\JoseBundle\Model\JWKManagerInterface $jwk_manager
     * @param string                                           $class
     * @param array                                            $keys
     */
    public function __construct(JWKManagerInterface $jwk_manager, $class, array $keys)
    {
        $this->class = $class;
        $this->jwk_manager = $jwk_manager;

        $this->public_jwkset = $this->createJWKSet();
        $this->private_jwkset = $this->createJWKSet();

        $this->loadKeys($keys);
    }

    protected function getSupportedMethods()
    {
        return array_merge(array(
            'findByKID',
        ), parent::getSupportedMethods());
    }

    protected function findByKID($header)
    {
        if (!array_key_exists('kid', $header)) {
            return;
        }
        $jwkset = $this->createJWKSet();
        foreach ($this->getPublicKeyset()->getKeys() as $key) {
            if ($header['kid'] === $key->getKeyID()) {
                return $jwkset->addKey($key);
            }
        }
        foreach ($this->getPrivateKeyset()->getKeys() as $key) {
            if ($header['kid'] === $key->getKeyID()) {
                return $jwkset->addKey($key);
            }
        }
    }

    /**
     * @return string
     */
    protected function loadKeys(array $keys)
    {
        foreach ($keys as $id => $key) {
            switch ($key['type']) {
                case 'rsa':
                    $public_key  = $this->getJWKManager()->loadKeyFromX509Certificate($key['public']['file']);
                    $private_key = $this->getJWKManager()->loadKeyFromX509Certificate($key['private']['file'], isset($key['private']['passphrase']) ? $key['private']['passphrase'] : null);

                    foreach (array('public' => $public_key, 'private' => $private_key) as $key_type => $jwk) {
                        $jwk->setValue('kid', $id);
                        if (isset($key[$key_type]['key_ops']) && !is_null($key[$key_type]['key_ops'])) {
                            $jwk->setValue('key_ops', $key[$key_type]['key_ops']);
                        }
                        foreach (array('alg', 'use') as $index) {
                            if (!is_null($key[$index])) {
                                $jwk->setValue($index, $key[$index]);
                            }
                        }
                    }

                    $this->getPublicKeyset()->addKey($public_key);
                    $this->getPrivateKeyset()->addKey($private_key);
                    break;
                case 'ecc':
                    $public_key  = $this->getJWKManager()->loadKeyFromECCCertificate($key['public']['file']);
                    $private_key = $this->getJWKManager()->loadKeyFromECCCertificate($key['private']['file'], isset($key['private']['passphrase']) ? $key['private']['passphrase'] : null);

                    foreach (array('public' => $public_key, 'private' => $private_key) as $key_type => $jwk) {
                        foreach (array('kid', 'key_ops') as $index) {
                            if (isset($key[$key_type][$index]) && !is_null($key[$key_type][$index])) {
                                $jwk->setValue($index, $key[$key_type][$index]);
                            }
                        }
                        foreach (array('alg', 'use') as $index) {
                            if (!is_null($key[$index])) {
                                $jwk->setValue($index, $key[$index]);
                            }
                        }
                    }

                    $this->getPublicKeyset()->addKey($public_key);
                    $this->getPrivateKeyset()->addKey($private_key);
                    break;
                /*case 'jwk':
                    $values = json_decode($key['value'], true);
                    if (is_null($values)) {
                        throw new \InvalidArgumentException('Bad JWK.');
                    }
                    $jwk = $this->getJWKManager()->loadKeyFromValues($values);
                    $jwk->setValue('kid', $id);
                    if (!is_null($key['use'])) {
                        $jwk->setValue('use', $key['use']);
                    }
                    if (!is_null($key['key_ops'])) {
                        $jwk->setValue('key_ops', $key['key_ops']);
                    }
                    if ($key['public']) {
                        $this->getPublicKeyset()->addKey($jwk);
                    } else {
                        $this->getPrivateKeyset()->addKey($jwk);
                    }
                    break;*/
            }
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
     * @param \SpomkyLabs\JoseBundle\Model\JWKSetInterface $jwk_set
     *
     * @return $this
     */
    public function setPrivateKeyset(JWKSetInterface $jwk_set)
    {
        $this->private_jwkset = $jwk_set;

        return $this;
    }

    /**
     * @inheritdoc()
     */
    public function getPrivateKeyset()
    {
        return $this->private_jwkset;
    }

    /**
     * @param \SpomkyLabs\JoseBundle\Model\JWKSetInterface $jwk_set
     *
     * @return $this
     */
    public function setPublicKeyset(JWKSetInterface $jwk_set)
    {
        $this->public_jwkset = $jwk_set;

        return $this;
    }

    /**
     * @inheritdoc()
     */
    public function getPublicKeyset()
    {
        return $this->public_jwkset;
    }

    /**
     * @param array $values
     *
     * @return \SpomkyLabs\JoseBundle\Model\JWKSetInterface
     */
    public function createJWKSet(array $values = array())
    {
        $class = $this->getClass();
        /**
         * @var \SpomkyLabs\JoseBundle\Model\JWKSetInterface
         */
        $jwk_set = new $class();

        foreach ($values as $value) {
            $jwk = $this->getJWKManager()->createJWK($value);
            $jwk_set->addKey($jwk);
        }

        return $jwk_set;
    }

    /**
     * @inheritdoc()
     */
    protected function getJWKManager()
    {
        return $this->jwk_manager;
    }

    /**
     * @inheritdoc()
     */
    public function findKeyById($kid, $public)
    {
        if (true === $public) {
            foreach ($this->getPublicKeyset()->getKeys() as $key) {
                if ($kid === $key->getKeyID()) {
                    return $key;
                }
            }
        } else {
            foreach ($this->getPrivateKeyset()->getKeys() as $key) {
                if ($kid === $key->getKeyID()) {
                    return $key;
                }
            }
        }
    }
}
