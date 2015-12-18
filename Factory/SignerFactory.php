<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Factory;

use Jose\Algorithm\JWAManagerInterface;
use Jose\Payload\PayloadConverterManagerInterface;
use Jose\Signer;

final class SignerFactory
{
    /**
     * @var \Jose\Payload\PayloadConverterManagerInterface
     */
    private $payload_converter;

    /**
     * DecrypterFactory constructor.
     *
     * @param \Jose\Payload\PayloadConverterManagerInterface $payload_converter
     */
    public function __construct(PayloadConverterManagerInterface $payload_converter)
    {
        $this->payload_converter = $payload_converter;
    }

    /**
     * @param \Jose\Algorithm\JWAManagerInterface $jwa_manager
     *
     * @return \Jose\Signer
     */
    public function createSigner(JWAManagerInterface $jwa_manager)
    {
        return new Signer($jwa_manager, $this->payload_converter);
    }
}
