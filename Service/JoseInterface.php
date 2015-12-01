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

use Jose\EncrypterInterface;
use Jose\JWKSetInterface;
use Jose\LoaderInterface;
use Jose\SignerInterface;
use SpomkyLabs\JoseBundle\Model\JotManagerInterface;

interface JoseInterface extends LoaderInterface, SignerInterface, EncrypterInterface
{
    /**
     * @return \Jose\LoaderInterface
     */
    public function getLoader();

    /**
     * @return \Jose\SignerInterface
     */
    public function getSigner();

    /**
     * @return \Jose\EncrypterInterface
     */
    public function getEncrypter();

    /**
     * @return null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface
     */
    public function getJotManager();

    /**
     * @param \SpomkyLabs\JoseBundle\Model\JotManagerInterface $jot_manager
     */
    public function setJotManager(JotManagerInterface $jot_manager);

    /**
     * {@inheritdoc}
     */
    public function checkJWT(&$input, JWKSetInterface $keyset = null, $detached_payload = null);
}
