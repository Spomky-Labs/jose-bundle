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

use Jose\Compression\CompressionManagerInterface;
use SpomkyLabs\JoseBundle\Model\JotManagerInterface;
use SpomkyLabs\JoseBundle\Service\Encrypter;

final class EncrypterFactory
{
    /**
     * @var \Jose\Compression\CompressionManagerInterface
     */
    private $compression_manager;

    /**
     * @var \SpomkyLabs\JoseBundle\Factory\JWAFactory
     */
    private $jwa_factory;

    /**
     * @var null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface
     */
    private $jot_manager;

    /**
     * EncrypterFactory constructor.
     *
     * @param \Jose\Compression\CompressionManagerInterface         $compression_manager
     * @param \SpomkyLabs\JoseBundle\Factory\JWAFactory             $jwa_factory
     * @param \SpomkyLabs\JoseBundle\Model\JotManagerInterface|null $jot_manager
     */
    public function __construct(CompressionManagerInterface $compression_manager,
                                JWAFactory $jwa_factory,
                                JotManagerInterface $jot_manager = null
    ) {
        $this->compression_manager = $compression_manager;
        $this->jwa_factory = $jwa_factory;
        $this->jot_manager = $jot_manager;
    }

    /**
     * @param string[] $algorithms
     *
     * @return \Jose\Encrypter
     */
    public function createEncrypter(array $algorithms)
    {
        $jwa_manager = $this->jwa_factory->createAlgorithmManager($algorithms);

        return new Encrypter($jwa_manager, $this->compression_manager, $this->jot_manager);
    }
}
