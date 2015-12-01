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
use Jose\JWKSetManagerInterface;

interface KeysetManagerInterface extends JWKSetManagerInterface
{
    const KEYSET_PUBLIC = 'public';
    const KEYSET_PRIVATE = 'private';
    const KEYSET_ASYMMETRIC = 'asymmetric';

    /**
     * @return \Jose\JWKSetInterface
     */
    public function getKeyset($keyset_name);

    /**
     * This is a convenient method for `getKeyset(KeysetManagerInterface::KEYSET_PUBLIC)`.
     *
     * @return \Jose\JWKSetInterface
     */
    public function getPublicKeyset();

    /**
     * This is a convenient method for `getKeyset(KeysetManagerInterface::KEYSET_PRIVATE)`.
     *
     * @return \Jose\JWKSetInterface
     */
    public function getPrivateKeyset();

    /**
     * This is a convenient method for `getKeyset(KeysetManagerInterface::KEYSET_ASYMMETRIC)`.
     *
     * @return \Jose\JWKSetInterface
     */
    public function getAsymmetricKeyset();

    /**
     * Returns a key set that contains all shared keys.
     *
     * @return \Jose\JWKSetInterface
     *
     * @see addKey
     */
    public function getSharedKeyset();

    /**
     * @param string   $id
     * @param string[] $keysets
     *
     * @return null|\Jose\JWKInterface
     */
    public function findKeyById($id, array $keysets = []);

    /**
     * @param \Jose\JWKInterface $key
     * @param bool               $is_shared
     *
     * @return self
     */
    public function addKey(JWKInterface $key, $is_shared = false);

    /**
     * @param string      $id
     * @param string      $filename
     * @param null|string $password
     * @param bool        $is_shared
     * @param bool        $load_public
     * @param array       $additional_data
     *
     * @return mixed
     */
    public function loadKeyFromFile($id, $filename, $password = null, $is_shared = false, $load_public = false, array $additional_data = []);

    /**
     * @param array $values
     * @param bool  $is_shared
     *
     * @return self
     */
    public function loadKeyFromValues(array $values, $is_shared = false);

    /**
     * @param string|array $jwkset
     * @param bool         $is_shared
     *
     * @return self
     */
    public function loadKeyFromJWKSet($jwkset, $is_shared = false);

    /**
     * @param string|array $jwk
     * @param bool         $is_shared
     *
     * @return self
     */
    public function loadKeyFromJWK($jwk, $is_shared = false);
}
