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

use Jose\Checker\CheckerManagerInterface;
use Jose\Verifier;

final class VerifierFactory
{
    /**
     * @var \Jose\Checker\CheckerManagerInterface
     */
    private $checker_manager;

    /**
     * @var \SpomkyLabs\JoseBundle\Factory\JWAFactory
     */
    private $jwa_factory;

    /**
     * VerifierFactory constructor.
     *
     * @param \Jose\Checker\CheckerManagerInterface     $checker_manager
     * @param \SpomkyLabs\JoseBundle\Factory\JWAFactory $jwa_factory
     */
    public function __construct(CheckerManagerInterface $checker_manager,
                                JWAFactory $jwa_factory
    )
    {
        $this->checker_manager = $checker_manager;
        $this->jwa_factory = $jwa_factory;
    }

    /**
     * @param string[] $algorithms
     *
     * @return \Jose\Verifier
     */
    public function createVerifier(array $algorithms)
    {
        $jwa_manager = $this->jwa_factory->createAlgorithmManager($algorithms);
        return new Verifier($jwa_manager, $this->checker_manager);
    }
}
