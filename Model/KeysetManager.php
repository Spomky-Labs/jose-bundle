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

use Jose\JWKInterface;
use SpomkyLabs\Jose\JWKSetManager;
use SpomkyLabs\Jose\KeyConverter\KeyConverter;

class KeysetManager extends JWKSetManager implements KeysetManagerInterface
{
    /**
     * @var \Jose\JWKSetInterface
     */
    protected $private_keyset;

    /**
     * @var \Jose\JWKSetInterface
     */
    protected $public_keyset;

    /**
     * @var \Jose\JWKSetInterface
     */
    protected $asymmetric_keyset;

    /**
     * @var \Jose\JWKSetInterface
     */
    protected $shared_keyset;

    /**
     * @return \string[]
     */
    public function getKeysetNames()
    {
        return [
            self::KEYSET_PRIVATE,
            self::KEYSET_PUBLIC,
            self::KEYSET_ASYMMETRIC,
        ];
    }

    /**
     *
     */
    public function __construct()
    {
        $this->private_keyset = $this->createJWKSet();
        $this->public_keyset = $this->createJWKSet();
        $this->asymmetric_keyset = $this->createJWKSet();
        $this->shared_keyset = $this->createJWKSet();
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyset($name)
    {
        if (!in_array($name, $this->getKeysetNames())) {
            throw new \InvalidArgumentException(sprintf('Unknown key set. Please use one of the following name: %s', json_encode($this->getKeysetNames())));
        }
        $method = 'get'. ucfirst($name).'Keyset';
        if (!in_array($name, $this->getKeysetNames())) {
            throw new \RuntimeException(sprintf('Unknown method "%s".', $method));
        }

        return $this->$method();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicKeyset()
    {
        return $this->public_keyset;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivateKeyset()
    {
        return $this->private_keyset;
    }

    /**
     * {@inheritdoc}
     */
    public function getAsymmetricKeyset()
    {
        return $this->asymmetric_keyset;
    }

    /**
     * {@inheritdoc}
     */
    public function getSharedKeyset()
    {
        return $this->shared_keyset;
    }

    /**
     * {@inheritdoc}
     */
    public function findKeyById($id, array $keysets = [])
    {
        if (empty($keysets)) {
            $keysets = $this->getKeysetNames();
        }
        foreach ($keysets as $name) {
            $keyset = $this->getKeyset($name);
            foreach ($keyset->getKeys() as $key) {
                if ($key->getKeyID()) {
                    return $key;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addKey(JWKInterface $key, $is_shared = false)
    {
        $type = $key->getKeyType();
        if (null === $type) {
            throw new \InvalidArgumentException('The key has not type.');
        }
        switch ($type) {
            case 'RSA':
            case 'EC':
                if (null !== $key->getValue('d')) {
                    $this->private_keyset->addKey($key);
                } else {
                    $this->public_keyset->addKey($key);
                }
                break;
            case 'dir':
            case 'oct':
                $this->asymmetric_keyset->addKey($key);
                break;
        }
        if (true === $is_shared) {
            $this->shared_keyset->addKey($key);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function loadKeyFromFile($filename, $password = null, $is_shared = false)
    {
        $values = KeyConverter::loadKeyFromFile($filename, $password);
        return $this->loadKeyFromValues($values, $is_shared);
    }

    /**
     * {@inheritdoc}
     */
    public function loadKeyFromJWKSet($jwkset, $is_shared = false)
    {
        if (is_string($jwkset)) {
            $jwkset = json_decode($jwkset, true);
        }
        if (!is_array($jwkset)) {
            throw new \InvalidArgumentException('Not a valid JWKSet');
        }
        $key_set = $this->createJWKSet($jwkset);
        foreach ($key_set->getKeys() as $key) {
            $this->addKey($key, $is_shared);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function loadKeyFromJWK($jwk, $is_shared = false)
    {
        if (is_string($jwk)) {
            $jwk = json_decode($jwk, true);
        }
        if (!is_array($jwk)) {
            throw new \InvalidArgumentException('Not a valid JWK');
        }
        return $this->loadKeyFromValues($jwk, $is_shared);
    }

    /**
     * {@inheritdoc}
     */
    public function loadKeyFromValues(array $values, $is_shared = false)
    {
        $key = $this->getJWKManager()->createJWK($values);

        return $this->addKey($key, $is_shared);
    }
}
