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
use Jose\JWKSetInterface;
use Jose\KeyConverter\ECKey;
use Jose\KeyConverter\KeyConverter;
use Jose\KeyConverter\RSAKey;

class KeyStorage implements KeyStorageInterface
{
    /**
     * @var array[]
     */
    protected $none_keys = [];

    /**
     * @var array[]
     */
    protected $private_keys = [];

    /**
     * @var array[]
     */
    protected $public_keys = [];

    /**
     * @var array[]
     */
    protected $direct_keys = [];

    /**
     * @var array[]
     */
    protected $symmetric_keys = [];

    /**
     * @var array[]
     */
    protected $shared_keys = [];

    public function __construct(array $keys)
    {
        foreach ($keys as $id => $key) {
            $result = [];
            if (!empty($key['file'])) {
                $result = $this->loadKeyFile(
                    $key['file'],
                    $key['passphrase'],
                    $key['values']
                );
            } elseif (!empty($key['certificate'])) {
                $result = $this->loadKeyFile(
                    $key['certificate'],
                    $key['values']
                );
            } elseif (!empty($key['jwk'])) {
                $result = $this->loadJWK(
                    $key['jwk']
                );
            } elseif (!empty($key['jwkset'])) {
                $result = $this->loadJWKSet(
                    $key['jwkset']
                );
            }
            $this->addKey($result, $key['values'], $key['load_public_key'], $key['shared']);
        }
    }

    private function addKey(array $values, array $additional_values, $load_public_key, $shared)
    {
        if (array_key_exists('keys', $values)) {
            foreach ($values['keys'] as $key) {
                $this->addKey($key, $additional_values, $load_public_key, $shared);
            }
        } else {
            $key = array_merge($additional_values, $values);
            if (!array_key_exists('kty', $key)) {
                throw new \InvalidArgumentException('Invalid key type');
            }

            switch ($key['kty']) {
                case 'EC':
                    if (array_key_exists('d', $key)) {
                        $this->private_keys[] = $key;
                        if (true === $load_public_key) {
                            $pub = ECKey::toPublic(new ECKey($key))->toArray();
                            $this->addKey($pub, [], $load_public_key, $shared);
                        }
                    } else {
                        $this->public_keys[] = $key;
                        if (true === $shared) {
                            $this->shared_keys[] = $key;
                        }
                    }
                    break;
                case 'RSA':
                    if (array_key_exists('d', $key)) {
                        $this->private_keys[] = $key;
                        if (true === $load_public_key) {
                            $pub = RSAKey::toPublic(new RSAKey($key))->toArray();
                            $this->addKey($pub, [], $load_public_key, $shared);
                        }
                    } else {
                        $this->public_keys[] = $key;
                        if (true === $shared) {
                            $this->shared_keys[] = $key;
                        }
                    }
                    break;
                case 'none':
                    $this->none_keys[] = $key;
                    break;
                case 'dir':
                    $this->direct_keys[] = $key;
                    break;
                case 'oct':
                    $this->symmetric_keys[] = $key;
                default:
                    throw new \InvalidArgumentException('Unsupported key type');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return array_merge(
            $this->getPrivateKeys(),
            $this->getPublicKeys(),
            $this->getSymmetricKeys(),
            $this->getDirectKeys()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNoneKeys()
    {
        return $this->none_keys;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicKeys()
    {
        return $this->public_keys;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivateKeys()
    {
        return $this->private_keys;
    }

    /**
     * {@inheritdoc}
     */
    public function getSharedKeys()
    {
        return $this->shared_keys;
    }

    /**
     * {@inheritdoc}
     */
    public function getSymmetricKeys()
    {
        return $this->symmetric_keys;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectKeys()
    {
        return $this->direct_keys;
    }

    /**
     * {@inheritdoc}
     */
    public function loadCertificateFile($filename, array $additional_data = [])
    {
        $values = KeyConverter::loadKeyFromCertificateFile($filename);

        return array_merge($values, $additional_data);
    }

    /**
     * {@inheritdoc}
     */
    public function loadKeyFile($filename, $password = null, array $additional_data = [])
    {
        $values = KeyConverter::loadKeyFromFile($filename, $password);

        return array_merge($values, $additional_data);
    }

    /**
     * {@inheritdoc}
     */
    public function loadJWKSet($jwkset)
    {
        if (is_string($jwkset)) {
            $jwkset = json_decode($jwkset, true);
        } elseif ($jwkset instanceof JWKSetInterface) {
            $jwkset = json_decode($jwkset->jsonSerialize(), true);
        }
        if (!is_array($jwkset) || !array_key_exists('keys', $jwkset)) {
            throw new \InvalidArgumentException('Not a valid JWKSet');
        }

        return $jwkset;
    }

    /**
     * {@inheritdoc}
     */
    public function loadJWK($jwk)
    {
        if (is_string($jwk)) {
            $jwk = json_decode($jwk, true);
        } elseif ($jwk instanceof JWKInterface) {
            $jwk = $jwk->getValues();
        }
        if (!is_array($jwk)) {
            throw new \InvalidArgumentException('Not a valid JWK');
        }

        return $jwk;
    }
}
