<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Controller;

use Jose\Behaviour\HasJWKSetManager;
use SpomkyLabs\JoseBundle\Model\KeyStorageInterface;
use Symfony\Component\HttpFoundation\Response;

final class KeyController
{
    use HasJWKSetManager;
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

    public function getPublicKeysetAction()
    {
        $keys = ['keys' => $this->getKeyStorage()->getSharedKeys()];
        $jwkset = $this->getJWKSetManager()->createJWKSet($keys);

        return new Response(
            json_encode($jwkset),
            200,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    /**
     * @return \SpomkyLabs\JoseBundle\Model\KeyStorageInterface
     */
    protected function getKeyStorage()
    {
        return $this->key_storage;
    }
}
