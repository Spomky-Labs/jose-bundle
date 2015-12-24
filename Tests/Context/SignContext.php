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
use Jose\Object\JWK;
use Jose\Object\SignatureInstruction;

/**
 * Behat context class.
 */
trait SignContext
{
    /**
     * @var string
     */
    private $signed_data = null;

    /**
     * @var \Jose\Object\JWKInterface
     */
    private $signature_key;

    /**
     * @var bool
     */
    private $is_signature_detached = false;

    /**
     * @var null|string
     */
    private $detached_payload;

    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @Given I want to use the following key to sign the message
     */
    public function iWantToUseTheFollowingKeyToSignTheMessage(PyStringNode $lines)
    {
        $data = [];
        foreach ($lines->getStrings() as $line) {
            list($key, $value) = explode(':', $line);
            $data[$key] = $value;
        }
        $jwk = new JWK($data);
        $this->signature_key = $jwk;
    }

    /**
     * @Given I add value :value at key :key in the protected header
     */
    public function iAddValueAtKeyInTheProtectedHeader($key, $value)
    {
        $this->protected_header[$key] = $value;
    }

    /**
     * @Given I add value :value at key :key in the unprotected header
     */
    public function iAddValueAtKeyInTheUnprotectedHeader($key, $value)
    {
        $this->unprotected_header[$key] = $value;
    }

    /**
     * @When I add the claim :claim with value :value
     */
    public function iAddTheClaim($claim, $value)
    {
        if (null !== $this->input && !is_array($this->input)) {
            throw new \InvalidArgumentException('Input data already assigned.');
        }
        if (null === $this->input) {
            $this->input = [];
        }
        $this->input[$claim] = $value;
    }

    /**
     * @When the payload is :message
     */
    public function thePayloadIs($message)
    {
        if (null !== $this->input) {
            throw new \InvalidArgumentException('Input data already assigned.');
        }
        $this->input = $message;
    }

    /**
     * @When I try to sign the input
     */
    public function iTryToSignTheInput()
    {
        $instruction = new SignatureInstruction(
            $this->signature_key,
            $this->protected_header,
            $this->unprotected_header
        );
        $this->signed_data = $this->getSigner()->sign($this->input, [$instruction], $this->serialization_mode, $this->is_signature_detached, $this->detached_payload);
    }

    /**
     * @Then the signed message is :result
     */
    public function theSignedMessageIs($result)
    {
        if ($result !== $this->signed_data) {
            throw new \Exception(sprintf('The current result did not matched the expected result. Got "%s"', $this->signed_data));
        }
    }

    /**
     * @return \Jose\SignerInterface
     */
    private function getSigner()
    {
        return $this->getContainer()->get('jose');
    }

    /**
     * @Given the signature is detached
     */
    public function theSignatureIsDetached()
    {
        $this->is_signature_detached = true;
    }

    /**
     * @Given the signature is attached
     */
    public function theSignatureIsAttached()
    {
        $this->is_signature_detached = false;
    }

    /**
     * @Then the result is a signed JWT
     */
    public function theResultIsASignedJwt()
    {
        if (!is_string($this->signed_data)) {
            throw new \Exception('The result is not a string');
        }
    }

    /**
     * @Then the detached payload is not null
     */
    public function theDetachedPayloadIsNotNull()
    {
        if (null === $this->detached_payload) {
            throw new \Exception('The detached payload is null');
        }
    }

    /**
     * @Then the detached payload is null
     */
    public function theDetachedPayloadIsNull()
    {
        if (null !== $this->detached_payload) {
            throw new \Exception(sprintf('The detached payload is not null. Its value is "%s"', $this->detached_payload));
        }
    }

    /**
     * @Then the signed data contains :pattern
     */
    public function theSignedDataContains($pattern)
    {
        if (1 !== preg_match($pattern, $this->signed_data)) {
            throw new \Exception(sprintf('The signed data does not contain the expected pattern. Its value is "%s".', $this->signed_data));
        }
    }
}
