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

class Jot implements JotInterface
{
    /**
     * @var string
     */
    protected $data = '';

    /**
     * @var int
     */
    protected $expires_at;

    /**
     * @var string
     */
    protected $jti;

    /**
     * Jot constructor.
     */
    public function __construct()
    {
        $this->jti = base_convert(hash('sha512', uniqid(mt_rand(), true)), 16, 36);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getJti()
    {
        return $this->jti;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return $this->expires_at < time();
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt($expires_at)
    {
        $this->expires_at = $expires_at;
    }
}
