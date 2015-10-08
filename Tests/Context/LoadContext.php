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
use Jose\JWTInterface;

/**
 * Behat context class.
 */
trait LoadContext
{
    /**
     * @var null|mixed|array|\Jose\JWTInterface|\Jose\JWSInterface|\Jose\JWEInterface|\Jose\JWKInterface|\Jose\JWKSetInterface
     */
    private $loaded_data;

    /**
     * @var null|\Exception
     */
    private $exception;

    /**
     * @return \Jose\JWKSetInterface
     */
    abstract protected function getKeyset();

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
     * @When I try to load the following data
     */
    public function iTryToLoadTheFollowingData(PyStringNode $lines)
    {
        if (1 !== count($lines->getStrings())) {
            throw new \Exception('Please set only one line for this test.');
        }

        foreach($lines->getStrings() as $data) {
            try {
                $this->loaded_data = $this->getLoader()->load($data, $this->getKeyset());
            } catch (\Exception $e) {
                $this->exception = $e;
            }
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
     * @return \SpomkyLabs\JoseBundle\Service\Jose
     */
    private function getLoader()
    {
        return $this->getContainer()->get('jose');
    }

    /**
     * @Then I should receive an exception
     */
    public function iShouldReceiveAnException()
    {
        if ($this->loaded_data instanceof \Exception) {
            throw new \Exception(sprintf('The loaded data is not an exception. Its class is %s', get_class($this->loaded_data)));
        }
    }

    /**
     * @Then the exception message is :message
     */
    public function theExceptionMessageIs($message)
    {
        if ($message !== $this->exception->getMessage()) {
            throw new \Exception(sprintf('The exception message is "%s"', $message));
        }
    }

    /**
     * @Then the JWT :position :parameter is not null
     */
    public function theJwtParameterIsNotNull($position, $parameter)
    {
        if (!in_array($position, ['header', 'payload'])) {
            throw new \Exception(sprintf('Supported positions are "%s"', json_encode(['header', 'payload'])));
        }
        $value = 'header' === $position?$this->loaded_data->getHeaderValue($parameter):$this->loaded_data->getPayloadValue($parameter);
        if (null === $value) {
            throw new \Exception('The value is null');
        }
    }

    /**
     * @Then the JWT :position :parameter is null
     */
    public function theJwtParameterIsNull($position, $parameter)
    {
        if (!in_array($position, ['header', 'payload'])) {
            throw new \Exception(sprintf('Supported positions are "%s"', json_encode(['header', 'payload'])));
        }
        $value = 'header' === $position?$this->loaded_data->getHeaderValue($parameter):$this->loaded_data->getPayloadValue($parameter);
        if (null !== $value) {
            throw new \Exception(sprintf('The value is not null. Its value is "%s"', $value));
        }
    }
}
