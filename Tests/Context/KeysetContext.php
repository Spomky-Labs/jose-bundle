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
use Jose\JWEInterface;
use Jose\JWKInterface;
use Jose\JWKSetInterface;
use Jose\JWSInterface;

/**
 * Behat context class.
 */
trait KeysetContext
{
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
            $this->jwkset = $this->getKeysetManager()->createJWKSet();
        }
        $data = [];
        foreach ($lines->getStrings() as $line) {
            list($key,$value) = explode(':', $line);
            $data[$key] = $value;
        }
        $jwk = $this->getKeyManager()->createJWK($data);
        $this->jwkset->addKey($jwk);
    }

    /**
     * @return \Jose\JWKSetInterface
     */
    protected function getKeyset()
    {
        return $this->jwkset;
    }

    /**
     * @return \Jose\JWKSetManagerInterface
     */
    protected function getKeysetManager()
    {
        return $this->getContainer()->get('jose.jwkset_manager');
    }

    /**
     * @return \Jose\JWKManagerInterface
     */
    protected function getKeyManager()
    {
        return $this->getContainer()->get('jose.jwk_manager');
    }
}
