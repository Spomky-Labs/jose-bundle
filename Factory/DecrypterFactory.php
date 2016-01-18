<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Factory;

use Jose\Checker\CheckerManagerInterface;
use Jose\Compression\CompressionManagerInterface;
use Jose\Decrypter;
use Jose\Payload\PayloadConverterManagerInterface;

final class DecrypterFactory
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
     * @var \Jose\Checker\CheckerManagerInterface
     */
    private $checker_manager;

    /**
     * @var \SpomkyLabs\JoseBundle\Factory\JWAFactory
     */
    private $jwa_factory;

    /**
     * DecrypterFactory constructor.
     *
     * @param \Jose\Payload\PayloadConverterManagerInterface $payload_converter
     * @param \Jose\Compression\CompressionManagerInterface  $compression_manager
     * @param \Jose\Checker\CheckerManagerInterface          $checker_manager
     * @param \SpomkyLabs\JoseBundle\Factory\JWAFactory      $jwa_factory
     */
    public function __construct(PayloadConverterManagerInterface $payload_converter,
                                CompressionManagerInterface $compression_manager,
                                CheckerManagerInterface $checker_manager,
                                JWAFactory $jwa_factory
    ) {
        $this->checker_manager = $checker_manager;
        $this->compression_manager = $compression_manager;
        $this->payload_converter = $payload_converter;
        $this->jwa_factory = $jwa_factory;
    }

    /**
     * @param string[] $algorithms
     *
     * @return \Jose\Decrypter
     */
    public function createDecrypter(array $algorithms)
    {
        $jwa_manager = $this->jwa_factory->createAlgorithmManager($algorithms);

        return new Decrypter($jwa_manager, $this->payload_converter, $this->compression_manager, $this->checker_manager);
    }
}
