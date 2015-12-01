<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Finder;

use Jose\Finder\JWKFinderInterface;
use SpomkyLabs\JoseBundle\Model\KeyStorageInterface;

/**
 */
final class AlgorithmFinder implements JWKFinderInterface
{
    /**
     * @var \SpomkyLabs\JoseBundle\Model\KeyStorageInterface
     */
    private $key_storage;

    /**
     * @param \SpomkyLabs\JoseBundle\Model\KeyStorageInterface $key_storage
     */
    public function __construct(KeyStorageInterface $key_storage)
    {
        $this->key_storage = $key_storage;
    }

    /**
     * @return \SpomkyLabs\JoseBundle\Model\KeyStorageInterface
     */
    private function getKeyStorage()
    {
        return $this->key_storage;
    }

    /**
     * {@inheritdoc}
     */
    public function findJWK(array $header)
    {
        if (!isset($header['alg']) || !is_string($header['alg'])) {
            return;
        }

        $result = ['keys'=>[]];
        $keys = $this->getKeyStorage()->getKeys();

        foreach($keys as $key) {
            if (array_key_exists('alg', $key) && $header['alg'] === $key['alg']) {
                $result['keys'][] = $key;
            }
        }

        return empty($result['keys'])?null:$result;
    }
}
