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
use Jose\Checker\CheckerManagerInterface;
use Jose\Verifier;

final class VerifierFactory
{
    /**
     * @var \Jose\Checker\CheckerManagerInterface
     */
    private $checker_manager;

    /**
     * DecrypterFactory constructor.
     *
     * @param \Jose\Checker\CheckerManagerInterface          $checker_manager
     */
    public function __construct(CheckerManagerInterface $checker_manager)
    {
        $this->checker_manager = $checker_manager;
    }
    /**
     * @param \Jose\Algorithm\JWAManagerInterface $jwa_manager
     *
     * @return \Jose\Verifier
     */
    public function createVerifier(JWAManagerInterface $jwa_manager)
    {
        return new Verifier($jwa_manager, $this->checker_manager);
    }
}
