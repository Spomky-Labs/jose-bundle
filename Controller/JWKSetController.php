<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Controller;

use Jose\Object\JWKSetInterface;
use Symfony\Component\HttpFoundation\Response;

final class JWKSetController
{
    /**
     * @var \Jose\Object\JWKSetInterface
     */
    private $jwkset;

    /**
     * JWKSetController constructor.
     *
     * @param \Jose\Object\JWKSetInterface $jwkset
     */
    public function __construct(JWKSetInterface $jwkset)
    {
        $this->jwkset = $jwkset;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function jsonAction()
    {
        return new Response(json_encode($this->jwkset), Response::HTTP_OK, ['content-type' => 'application/jwk-set+json']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pemAction()
    {
        return new Response(json_encode($this->jwkset->toPEM()), Response::HTTP_OK, ['content-type' => 'application/json']);
    }
}
