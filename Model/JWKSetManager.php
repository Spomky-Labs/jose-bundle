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

use SpomkyLabs\Jose\JWKSetManager as Base;
use SpomkyLabs\Jose\KeyConverter\KeyConverter;

class JWKSetManager extends Base implements JWKSetManagerInterface
{
    /**
     * @var \Jose\JWKSetInterface
     */
    private $private_jwkset;

    /**
     * @var \Jose\JWKSetInterface
     */
    private $public_jwkset;

    public function __construct()
    {
        $this->private_jwkset = $this->createJWKSet();
        $this->public_jwkset = $this->createJWKSet();
    }

    /**
     * @return string[]
     */
    protected function getSupportedMethods()
    {
        return array_merge([
            'findByKID',
        ], parent::getSupportedMethods());
    }

    /**
     * @param $header
     *
     * @return null|\SpomkyLabs\Jose\JWKSet
     */
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
     * @param array $keys
     */
    public function loadKeys(array $keys)
    {
        foreach ($keys as $id => $key) {
            switch ($key['type']) {
                case 'rsa':
                case 'ecc':
                    $data = KeyConverter::loadKeyFromFile($key['private_file'], isset($key['passphrase']) ? $key['passphrase'] : null);
                    $jwk = $this->getJWKManager()->createJWK($data);
                    $jwk->setValue('kid', $id);
                    foreach (['key_ops', 'alg', 'use'] as $index) {
                        if (isset($key[$index])) {
                            $jwk->setValue($index, $key[$index]);
                        }
                    }
                    $this->private_jwkset->addKey($jwk);

                    $data = KeyConverter::loadKeyFromFile($key['public_file']);
                    $jwk = $this->getJWKManager()->createJWK($data);
                    $jwk->setValue('kid', $id);
                    foreach (['key_ops', 'alg', 'use'] as $index) {
                        if (isset($key[$index])) {
                            $jwk->setValue($index, $key[$index]);
                        }
                    }
                    $this->public_jwkset->addKey($jwk);
                    break;
                case 'jwk':
                    $values = json_decode($key['value'], true);
                    if (!is_array($values)) {
                        throw new \InvalidArgumentException('Bad JWK.');
                    }
                    $jwk = $this->getJWKManager()->createJWK($values);
                    $jwk->setValue('kid', $id);
                    foreach (['key_ops', 'alg', 'use'] as $index) {
                        if (isset($key[$index])) {
                            $jwk->setValue($index, $key[$index]);
                        }
                    }
                    //if ($key['public']) {
                        $this->private_jwkset->addKey($jwk);
                    //} else {
                        $this->public_jwkset->addKey($jwk);
                    //}
                    break;
                case 'jwkset':
                    $values = json_decode($key['value'], true);
                    if (!is_array($values)) {
                        throw new \InvalidArgumentException('Bad JWK.');
                    }
                    $jwkset = $this->createJWKSet($values);
                    foreach ($jwkset as $jwk) {
                        $this->private_jwkset->addKey($jwk);
                    }
                    break;
                case 'shared':
                    $jwk = $this->getJWKManager()->createJWK([
                        'kid' => $id,
                        'kty' => 'oct',
                        'k'   => $key['value'],
                    ]);
                    foreach (['key_ops', 'alg', 'use'] as $index) {
                        if (isset($key[$index])) {
                            $jwk->setValue($index, $key[$index]);
                        }
                    }
                    break;
                case 'direct':
                    $jwk = $this->getJWKManager()->createJWK([
                        'kid'   => $id,
                        'kty'   => 'dir',
                        'dir'   => $key['value'],
                    ]);
                    foreach (['key_ops', 'alg', 'use'] as $index) {
                        if (isset($key[$index])) {
                            $jwk->setValue($index, $key[$index]);
                        }
                    }
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivateKeyset()
    {
        return $this->private_jwkset;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicKeyset()
    {
        return $this->public_jwkset;
    }

    /**
     * {@inheritdoc}
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
