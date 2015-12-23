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
use Jose\JSONSerializationModes;
use Jose\Object\JWK;
use Jose\Object\SignatureInstruction;

/**
 * Behat context class.
 */
trait ProcessContext
{
    /**
     * @var \Jose\Object\JWKInterface
     */
    private $signature_key = null;

    /**
     */
    private $signed_data = null;

    /**
     * @var array
     */
    private $protected_header = [];

    /**
     * @var array
     */
    private $unprotected_header = [];

    /**
     * @var array
     */
    private $serialization_mode = JSONSerializationModes::JSON_COMPACT_SERIALIZATION;

    /**
     * @var mixed
     */
    private $input = null;

    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @Given I have the following signature key
     */
    public function iHaveTheFollowingSignature(PyStringNode $lines)
    {
        $data = [];
        foreach ($lines->getStrings() as $line) {
            list($key, $value) = explode(':', $line);
            $data[$key] = $value;
        }
        $this->signature_key = new JWK($data);
    }

    /**
     * @Given I have an array as payload with the following value:
     */
    public function iHaveAnArrayAsPayloadWithTheFollowingValue(PyStringNode $string)
    {
        $this->input = json_decode($string->getRaw());
    }

    /**
     * @Given I have a protected header :header with value :value
     */
    public function iHaveAProtectedHeaderWithValue($header, $value)
    {
        $this->protected_header[$header] = $value;
    }

    /**
     * @When I try to sign the payload
     */
    public function iTryToSignThePayload()
    {
        /**
         * @var \Jose\SignerInterface
         */
        $signer = $this->getContainer()->get('jose.factory.signer')->createSigner(['RS256']);
        $instruction = new SignatureInstruction($this->signature_key, $this->protected_header, $this->unprotected_header);
        $this->signed_data = $signer->sign($this->input, [$instruction], JSONSerializationModes::JSON_COMPACT_SERIALIZATION);
    }

    /**
     * @Then the signed data is :data
     */
    public function theSignedDataIs($data)
    {
        if ($this->signed_data !== $data) {
            throw new \Exception(sprintf('The signed data is not the same as expected. I got "%s"', $this->signed_data));
        }
    }
}
