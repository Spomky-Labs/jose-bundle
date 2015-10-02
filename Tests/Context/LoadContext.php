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
trait LoadContext
{
    private $loaded_data;
    private $jwkset;

    /**
     * Returns Mink session.
     *
     * @param string|null $name name of the session OR active session will be used
     *
     * @return \Behat\Mink\Session
     */
    abstract public function getSession($name = null);

    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @Given I have the following public key in my public key set
     */
    public function iHaveTheFollowingPublicKeyInMyPublicKeySet(PyStringNode $lines)
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
     * @When I try to load the following data
     */
    public function iTryToLoadTheFollowingData(PyStringNode $lines)
    {
        if (1 !== count($lines->getStrings())) {
            throw new \Exception('Please set only one line for this test.');
        }

        foreach($lines->getStrings() as $data) {
            $this->loaded_data = $this->getLoader()->load($data);
        }
    }

    /**
     * @Then the loaded data is a JWS
     */
    public function theLoadedDataIsAJws()
    {
        if (!$this->loaded_data instanceof JWSInterface) {
            throw new \Exception(sprintf('The loaded data is not a JWS. Its class is %s', get_class($this->loaded_data)));
        }
    }

    /**
     * @Then the loaded data is a JWE
     */
    public function theLoadedDataIsAJwe()
    {
        if (!$this->loaded_data instanceof JWEInterface) {
            throw new \Exception(sprintf('The loaded data is not a JWE. Its class is %s', get_class($this->loaded_data)));
        }
    }

    /**
     * @Then the loaded data is a JWK
     */
    public function theLoadedDataIsAJwk()
    {
        if (!$this->loaded_data instanceof JWKInterface) {
            throw new \Exception(sprintf('The loaded data is not a JWK. Its class is %s', get_class($this->loaded_data)));
        }
    }

    /**
     * @Then the loaded data is a JWKSet
     */
    public function theLoadedDataIsAJwkset()
    {
        if (!$this->loaded_data instanceof JWKSetInterface) {
            throw new \Exception(sprintf('The loaded data is not a JWKSet. Its class is %s', get_class($this->loaded_data)));
        }
    }

    /**
     * @Then the signature of the loaded data is valid
     */
    public function theSignatureOfTheLoadedDataIsValid()
    {
        if (false === $this->getLoader()->verifySignature($this->loaded_data, $this->jwkset)) {
            throw new \Exception('The signature is not valid');
        }
    }

    /**
     * @Then the claims of the loaded data are valid
     */
    public function theClaimsOfTheLoadedDataAreValid()
    {
        $this->getLoader()->verify($this->loaded_data);
    }

    /**
     * @Then the payload of the loaded data is :payload
     */
    public function thePayloadOfTheLoadedDataIs($payload)
    {
        if ($payload !== $this->loaded_data->getPayload()) {
            throw new \Exception(sprintf('The payload is "%s"', $this->loaded_data->getPayload()));
        }
    }

    /**
     * @Then the algorithm of the loaded data is :alg
     */
    public function theAlgorithmOfTheLoadedDataIs($alg)
    {
        if ($alg !== $this->loaded_data->getAlgorithm()) {
            throw new \Exception(sprintf('The algorithm is "%s"', $this->loaded_data->getAlgorithm()));
        }
    }


    /**
     * @return \Jose\LoaderInterface
     */
    private function getLoader()
    {
        return $this->getContainer()->get('jose.loader');
    }

    /**
     * @return \Jose\JWKSetManagerInterface
     */
    private function getKeysetManager()
    {
        return $this->getContainer()->get('jose.jwkset_manager');
    }

    /**
     * @return \Jose\JWKManagerInterface
     */
    private function getKeyManager()
    {
        return $this->getContainer()->get('jose.jwk_manager');
    }
}
