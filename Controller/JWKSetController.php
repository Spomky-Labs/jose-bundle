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

use SpomkyLabs\JoseBundle\Model\JWKSetManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class JWKSetController
{
    protected $jwkset_manager;

    public function __construct(JWKSetManagerInterface $jwkset_manager)
    {
        $this->jwkset_manager = $jwkset_manager;
    }

    public function getPublicKeysetAction()
    {
        $jwkset = $this->getJWKSetManager()->getPublicKeyset();
        if (is_null($jwkset)) {
            $jwkset = [];
        }

        return new Response(
            json_encode($jwkset),
            200,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    protected function getJWKSetManager()
    {
        return $this->jwkset_manager;
    }
}
