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

use Jose\Payload\PayloadConverterManagerInterface;
use Jose\Signer;

final class SignerFactory
{
    /**
     * @var \Jose\Payload\PayloadConverterManagerInterface
     */
    private $payload_converter;

    /**
     * @var \SpomkyLabs\JoseBundle\Factory\JWAFactory
     */
    private $jwa_factory;

    /**
     * SignerFactory constructor.
     *
     * @param \Jose\Payload\PayloadConverterManagerInterface $payload_converter
     * @param \SpomkyLabs\JoseBundle\Factory\JWAFactory      $jwa_factory
     */
    public function __construct(PayloadConverterManagerInterface $payload_converter,
                                JWAFactory $jwa_factory
    )
    {
        $this->payload_converter = $payload_converter;
        $this->jwa_factory = $jwa_factory;
    }

    /**
     * @param string[] $algorithms
     *
     * @return \Jose\Signer
     */
    public function createSigner(array $algorithms)
    {
        $jwa_manager = $this->jwa_factory->createAlgorithmManager($algorithms);
        return new Signer($jwa_manager, $this->payload_converter);
    }
}
