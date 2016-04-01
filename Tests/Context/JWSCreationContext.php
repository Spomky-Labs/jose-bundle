<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Features\Context;

use Behat\Gherkin\Node\PyStringNode;
use Jose\Object\JWEInterface;
use Jose\Object\JWSInterface;

/**
 * Behat context trait.
 */
trait JWSCreationContext
{
    /**
     * @var array
     */
    private $header;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @Given I have the following values in the header
     */
    public function iHaveTheFollowingValuesInTheHeader(PyStringNode $string)
    {
        $this->header = json_decode($string->getRaw(), true);
    }

    /**
     * @Given I have the following payload
     */
    public function iHaveTheFollowingPayload(PyStringNode $string)
    {
        $this->payload = $string->getRaw();
    }

    /**
     * @When I try to create a JWS in JSON Compact Serialization Mode with signature key :key_service and I store the result in the variable :variable
     */
    public function iTryToCreateAJwsInJsonCompactSerializationModeWithSignatureKeyAndIStoreTheResultInTheVariable($key_service, $variable)
    {
        /**
         * @var $jws_creator \Jose\Factory\JWSFactory
         */
        $jws_creator = $this->getContainer()->get('jose.factory.jws');
        $key = $this->getContainer()->get($key_service);
        $this->$variable = $jws_creator->createJWSToCompactJSON($this->payload, $key, $this->header);
    }

    /**
     * @When I try to create a JWS in JSON Flattened Serialization Mode with signature key :key_service and I store the result in the variable :variable
     */
    public function iTryToCreateAJwsInJsonFlattenedSerializationModeWithSignatureKeyAndIStoreTheResultInTheVariable($key_service, $variable)
    {
        /**
         * @var $jws_creator \Jose\Factory\JWSFactory
         */
        $jws_creator = $this->getContainer()->get('jose.factory.jws');
        $key = $this->getContainer()->get($key_service);
        $this->$variable = $jws_creator->createJWSToFlattenedJSON($this->payload, $key, $this->header);
    }

    /**
     * @Then the variable :variable should be a string with value :value
     */
    public function theVariableShouldBeAStringWithValue($variable, $value)
    {
        if ($value !== $this->$variable) {
            throw new \Exception(sprintf(
                'The value of the variable "%s" is "%s"',
                $variable,
                $this->$variable
            ));
        }
    }
}
