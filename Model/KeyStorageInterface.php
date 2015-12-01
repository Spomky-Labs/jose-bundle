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

interface KeyStorageInterface
{
    /**
     * @return array
     */
    public function getKeys();

    /**
     * @return array
     */
    public function getNoneKeys();

    /**
     * @return array
     */
    public function getPublicKeys();

    /**
     * @return array
     */
    public function getPrivateKeys();

    /**
     * @return array
     */
    public function getSharedKeys();

    /**
     * @return array
     */
    public function getSymmetricKeys();

    /**
     * @return array
     */
    public function getDirectKeys();

    /**
     * @param string $filename
     * @param array  $additional_data
     *
     * @return mixed
     */
    public function loadCertificateFile($filename, array $additional_data = []);

    /**
     * @param string      $filename
     * @param null|string $password
     * @param array       $additional_data
     *
     * @return array
     */
    public function loadKeyFile($filename, $password = null, array $additional_data = []);

    /**
     * @param string|array|\Jose\JWKSetInterface $jwkset
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function loadJWKSet($jwkset);

    /**
     * @param string|array|\Jose\JWKInterface $jwk
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function loadJWK($jwk);
}
