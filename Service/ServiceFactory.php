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

use Jose\Factory\CheckerManagerFactory;
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
     * @var \SpomkyLabs\JoseBundle\Service\CheckerManager
     */
    private $checker_manager;

    /**
     * ServiceFactory constructor.
     *
     * @param \SpomkyLabs\JoseBundle\Service\AlgorithmManager   $algorithm_manager
     * @param \SpomkyLabs\JoseBundle\Service\CompressionManager $compression_manager
     * @param \SpomkyLabs\JoseBundle\Service\CheckerManager     $checker_manager
     */
    public function __construct(AlgorithmManager $algorithm_manager, CompressionManager $compression_manager, CheckerManager $checker_manager)
    {
        $this->algorithm_manager = $algorithm_manager;
        $this->compression_manager = $compression_manager;
        $this->checker_manager = $checker_manager;
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

    /**
     * @param string[]                      $selected_claim_checkers
     * @param string[]                      $selected_header_checkers
     * @param \Psr\Log\LoggerInterface|null $logger
     *
     * @return \Jose\Checker\CheckerManagerInterface
     */
    public function createChecker(array $selected_claim_checkers, array $selected_header_checkers, LoggerInterface $logger = null)
    {
        $claim_checkers = $this->checker_manager->getSelectedClaimChecker($selected_claim_checkers);
        $header_checkers = $this->checker_manager->getSelectedHeaderChecker($selected_header_checkers);
        
        return CheckerManagerFactory::createClaimCheckerManager($claim_checkers, $header_checkers);
    }
}
