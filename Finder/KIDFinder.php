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
final class KIDFinder implements JWKFinderInterface
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
        if (!isset($header['kid']) || !is_string($header['kid'])) {
            return;
        }

        $keys = $this->getKeyStorage()->getKeys();

        foreach ($keys as $key) {
            if (array_key_exists('kid', $key) && $header['kid'] === $key['kid']) {
                $result['keys'][] = $key;
            }
        }
    }
}
