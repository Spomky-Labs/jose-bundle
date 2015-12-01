<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Features\Context;

use Behat\Gherkin\Node\PyStringNode;
use Jose\Behaviour\HasJWKManager;
use Jose\Behaviour\HasJWKSetManager;
use Jose\JWKSetInterface;

/**
 * Behat context class.
 */
trait KeysetContext
{
    use HasJWKManager;
    use HasJWKSetManager;

    private $jwkset;

    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @Given I have the following key in my key set
     */
    public function iHaveTheFollowingKeyInMyKeySet(PyStringNode $lines)
    {
        if (!$this->jwkset instanceof JWKSetInterface) {
            $this->jwkset = $this->getJWKSetManager()->createJWKSet();
        }
        $data = [];
        foreach ($lines->getStrings() as $line) {
            list($key,$value) = explode(':', $line);
            $data[$key] = $value;
        }
        $jwk = $this->getJWKManager()->createJWK($data);
        $this->jwkset->addKey($jwk);
    }

    /**
     * @return \Jose\JWKSetInterface
     */
    protected function getKeyset()
    {
        return $this->jwkset;
    }
}
