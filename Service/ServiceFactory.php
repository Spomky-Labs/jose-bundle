<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Service;

use Jose\Factory\DecrypterFactory;
use Jose\Factory\EncrypterFactory;
use Jose\Factory\SignerFactory;
use Jose\Factory\VerifierFactory;
use Psr\Log\LoggerInterface;

final class ServiceFactory
{
    /**
     * @var \SpomkyLabs\JoseBundle\Service\AlgorithmManager
     */
    private $algorithm_manager;

    /**
     * @var \SpomkyLabs\JoseBundle\Service\CompressionManager
     */
    private $compression_manager;

    /**
     * ServiceFactory constructor.
     *
     * @param \SpomkyLabs\JoseBundle\Service\AlgorithmManager   $algorithm_manager
     * @param \SpomkyLabs\JoseBundle\Service\CompressionManager $compression_manager
     */
    public function __construct(AlgorithmManager $algorithm_manager, CompressionManager $compression_manager)
    {
        $this->algorithm_manager = $algorithm_manager;
        $this->compression_manager = $compression_manager;
    }

    /**
     * @param string[]                      $selected_algorithms
     * @param string[]                      $selected_compression_methods
     * @param \Psr\Log\LoggerInterface|null $logger
     *
     * @return \Jose\EncrypterInterface
     */
    public function createEncrypter(array $selected_algorithms, array $selected_compression_methods, LoggerInterface $logger = null)
    {
        $algorithms = $this->algorithm_manager->getSelectedAlgorithmMethods($selected_algorithms);
        $compression_methods = $this->compression_manager->getSelectedCompressionMethods($selected_compression_methods);

        return EncrypterFactory::createEncrypter($algorithms, $compression_methods, $logger);
    }

    /**
     * @param string[]                      $selected_algorithms
     * @param string[]                      $selected_compression_methods
     * @param \Psr\Log\LoggerInterface|null $logger
     *
     * @return \Jose\DecrypterInterface
     */
    public function createDecrypter(array $selected_algorithms, array $selected_compression_methods, LoggerInterface $logger = null)
    {
        $algorithms = $this->algorithm_manager->getSelectedAlgorithmMethods($selected_algorithms);
        $compression_methods = $this->compression_manager->getSelectedCompressionMethods($selected_compression_methods);

        return DecrypterFactory::createDecrypter($algorithms, $compression_methods, $logger);
    }

    /**
     * @param string[]                      $selected_algorithms
     * @param \Psr\Log\LoggerInterface|null $logger
     *
     * @return \Jose\SignerInterface
     */
    public function createSigner(array $selected_algorithms, LoggerInterface $logger = null)
    {
        $algorithms = $this->algorithm_manager->getSelectedAlgorithmMethods($selected_algorithms);

        return SignerFactory::createSigner($algorithms, $logger);
    }

    /**
     * @param string[]                      $selected_algorithms
     * @param \Psr\Log\LoggerInterface|null $logger
     *
     * @return \Jose\VerifierInterface
     */
    public function createVerifier(array $selected_algorithms, LoggerInterface $logger = null)
    {
        $algorithms = $this->algorithm_manager->getSelectedAlgorithmMethods($selected_algorithms);
        
        return VerifierFactory::createVerifier($algorithms, $logger);
    }
}
