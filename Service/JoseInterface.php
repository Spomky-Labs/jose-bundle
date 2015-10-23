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

interface JoseInterface extends LoaderInterface, SignerInterface, EncrypterInterface
{
    /**
     * @return \Jose\LoaderInterface
     */
    public function getLoader();

    /**
     * @param \Jose\LoaderInterface $loader
     *
     * @return self
     */
    public function setLoader(LoaderInterface $loader);

    /**
     * @return \Jose\SignerInterface
     */
    public function getSigner();

    /**
     * @param \Jose\SignerInterface $signer
     *
     * @return self
     */
    public function setSigner(SignerInterface $signer);

    /**
     * @return \Jose\EncrypterInterface
     */
    public function getEncrypter();

    /**
     * @param \Jose\EncrypterInterface $encrypter
     *
     * @return self
     */
    public function setEncrypter(EncrypterInterface $encrypter);

    /**
     * @return null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface
     */
    public function getJotManager();

    /**
     * @param null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface $jot_manager
     *
     * @return self
     */
    public function setJotManager($jot_manager);

    /**
     * {@inheritdoc}
     */
    public function checkJWT(&$input, JWKSetInterface $keyset = null, $detached_payload = null);
}
