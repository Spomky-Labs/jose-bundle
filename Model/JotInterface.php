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
     * @return string
     */
    public function getData();

    /**
     * @param string $data
     *
     * @return \SpomkyLabs\JoseBundle\Model\JotInterface
     */
    public function withData($data);
}
