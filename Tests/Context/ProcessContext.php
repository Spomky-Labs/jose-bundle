<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Features\Context;

use Jose\JSONSerializationModes;

/**
 * Behat context class.
 */
trait ProcessContext
{
    /**
     * @var array
     */
    private $protected_header = [];

    /**
     * @var array
     */
    private $unprotected_header = [];

    /**
     * @var array
     */
    private $serialization_mode = JSONSerializationModes::JSON_COMPACT_SERIALIZATION;

    /**
     * @var null|string|array
     */
    private $input = null;
}
