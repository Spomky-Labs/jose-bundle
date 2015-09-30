<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Event;

class Events
{
    const JOSE_BEFORE_SIGNATURE = 'jose_before_signature';
    const JOSE_AFTER_SIGNATURE = 'jose_after_signature';
    const JOSE_BEFORE_ENCRYPTION = 'jose_before_encryption';
    const JOSE_AFTER_ENCRYPTION = 'jose_after_encryption';
    const JOSE_BEFORE_LOADING = 'jose_before_loading';
    const JOSE_AFTER_LOADING = 'jose_after_loading';
}
