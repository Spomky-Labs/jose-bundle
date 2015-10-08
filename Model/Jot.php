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
    protected $data;

    /**
     * @var string
     */
    protected $jti;

    public function __construct()
    {
        $this->setJti(base_convert(hash('sha512', uniqid(mt_rand(), true)), 16, 36));
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
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
    public function setJti($jti)
    {
        $this->jti = $jti;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJti()
    {
        return $this->jti;
    }
}
