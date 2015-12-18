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
use Jose\Compression\CompressionManagerInterface;
use Jose\Encrypter;
use Jose\Payload\PayloadConverterManagerInterface;

final class EncrypterFactory
{
    /**
     * @var \Jose\Payload\PayloadConverterManagerInterface
     */
    private $payload_converter;

    /**
     * @var \Jose\Compression\CompressionManagerInterface
     */
    private $compression_manager;

    /**
     * DecrypterFactory constructor.
     *
     * @param \Jose\Payload\PayloadConverterManagerInterface $payload_converter
     * @param \Jose\Compression\CompressionManagerInterface  $compression_manager
     */
    public function __construct(PayloadConverterManagerInterface $payload_converter, CompressionManagerInterface $compression_manager)
    {
        $this->compression_manager = $compression_manager;
        $this->payload_converter = $payload_converter;
    }

    /**
     * @param \Jose\Algorithm\JWAManagerInterface $jwa_manager
     *
     * @return \Jose\Encrypter
     */
    public function createEncrypter(JWAManagerInterface $jwa_manager)
    {
        return new Encrypter($jwa_manager, $this->payload_converter, $this->compression_manager);
    }
}
