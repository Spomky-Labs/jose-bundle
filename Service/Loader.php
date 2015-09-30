<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Service;

use Jose\Compression\CompressionManagerInterface;
use Jose\JWAManagerInterface;
use Jose\JWKManagerInterface;
use Jose\JWKSetManagerInterface;
use Jose\JWTManagerInterface;
use SpomkyLabs\Jose\Loader as Base;

class Loader extends Base
{
    /**
     * @var \Jose\JWAManagerInterface
     */
    protected $jwa_manager;

    /**
     * @var \Jose\JWTManagerInterface
     */
    protected $jwt_manager;

    /**
     * @var \Jose\JWKManagerInterface
     */
    protected $jwk_manager;

    /**
     * @var \Jose\JWKSetManagerInterface
     */
    protected $jwkset_manager;

    /**
     * @var \Jose\Compression\CompressionManagerInterface
     */
    protected $compression_manager;

    /**
     * @return \Jose\JWAManagerInterface
     */
    protected function getJWAManager()
    {
        return $this->jwa_manager;
    }

    public function __construct(JWAManagerInterface $jwa_manager, JWTManagerInterface $jwt_manager, JWKManagerInterface $jwk_manager, JWKSetManagerInterface $jwkset_manager, CompressionManagerInterface $compression_manager)
    {
        $this->jwt_manager = $jwt_manager;
        $this->jwa_manager = $jwa_manager;
        $this->jwk_manager = $jwk_manager;
        $this->jwkset_manager = $jwkset_manager;
        $this->compression_manager = $compression_manager;
    }

    /**
     * @return \Jose\Compression\CompressionManagerInterface
     */
    protected function getCompressionManager()
    {
        return $this->compression_manager;
    }

    /**
     * @return \Jose\JWKManagerInterface
     */
    protected function getJWKManager()
    {
        return $this->jwk_manager;
    }

    /**
     * @return \Jose\JWKSetManagerInterface
     */
    protected function getJWKSetManager()
    {
        return $this->jwkset_manager;
    }

    /**
     * @return \Jose\JWTManagerInterface
     */
    protected function getJWTManager()
    {
        return $this->jwt_manager;
    }
}
