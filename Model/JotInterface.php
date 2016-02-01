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

interface JotInterface
{
    /**
     * @return string
     */
    public function getJti();

    /**
     * @return bool
     */
    public function isExpired();

    /**
     * @return int
     */
    public function getExpiresAt();

    /**
     * @param int $expires_at
     */
    public function setExpiresAt($expires_at);

    /**
     * @return string
     */
    public function getData();

    /**
     * @param string $data
     */
    public function setData($data);
}
