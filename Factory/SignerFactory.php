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

use SpomkyLabs\JoseBundle\Model\JotManagerInterface;
use SpomkyLabs\JoseBundle\Service\Signer;

final class SignerFactory
{
    /**
     * @var \SpomkyLabs\JoseBundle\Factory\JWAFactory
     */
    private $jwa_factory;

    /**
     * @var null|\SpomkyLabs\JoseBundle\Model\JotManagerInterface
     */
    private $jot_manager;

    /**
     * SignerFactory constructor.
     *
     * @param \SpomkyLabs\JoseBundle\Factory\JWAFactory             $jwa_factory
     * @param \SpomkyLabs\JoseBundle\Model\JotManagerInterface|null $jot_manager
     */
    public function __construct(JWAFactory $jwa_factory,
                                JotManagerInterface $jot_manager = null
    ) {
        $this->jwa_factory = $jwa_factory;
        $this->jot_manager = $jot_manager;
    }

    /**
     * @param string[] $algorithms
     *
     * @return \Jose\Signer
     */
    public function createSigner(array $algorithms)
    {
        $jwa_manager = $this->jwa_factory->createAlgorithmManager($algorithms);

        return new Signer($jwa_manager, $this->jot_manager);
    }
}
