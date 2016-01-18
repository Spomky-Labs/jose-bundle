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
use Jose\Object\EncryptionInstruction;
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
     * @var bool
     */
    private $is_signature_detached = false;

    /**
     * @var null|string
     */
    private $detached_payload;

    /**
     * @var string
     */
    private $encrypted_data = null;

    /**
     * @var \Jose\Object\JWKInterface
     */
    private $recipient_public_key;

    /**
     * @var null|\Jose\Object\JWKInterface
     */
    private $sender_private_key = null;

    /**
     * @var array
     */
    private $recipient_unprotected_header = [];

    /**
     * @var null|string
     */
    private $aad = null;

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
        /*
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

    /**
     * @Given I want to use the following key to encrypt a message
     */
    public function iWantToUseTheFollowingKeyToEncryptAMessage(PyStringNode $lines)
    {
        $data = [];
        foreach ($lines->getStrings() as $line) {
            list($key, $value) = explode(':', $line);
            $data[$key] = $value;
        }
        $jwk = new JWK($data);
        $this->recipient_public_key = $jwk;
    }

    /**
     * @When I try to encrypt the input
     */
    public function iTryToEncryptTheInput()
    {
        $instruction = new EncryptionInstruction(
            $this->recipient_public_key,
            $this->sender_private_key,
            $this->recipient_unprotected_header
        );
        $this->encrypted_data = $this->getEncrypter()->encrypt(
            $this->input,
            [$instruction],
            $this->protected_header,
            $this->unprotected_header,
            $this->serialization_mode,
            $this->aad
        );
    }

    /**
     * @Then the encrypted message is :result
     */
    public function theEncryptedMessageIs($result)
    {
        if ($result !== $this->encrypted_data) {
            throw new \Exception(sprintf('The current result did not matched the expected result. Got "%s"', $this->encrypted_data));
        }
    }

    /**
     * @return \Jose\EncrypterInterface
     */
    private function getEncrypter()
    {
        return $this->getContainer()->get('jose');
    }

    /**
     * @Then the result is an encrypted JWT
     */
    public function theResultIsAnEncryptedJwt()
    {
        if (!is_string($this->encrypted_data)) {
            throw new \Exception('The result is not a string');
        }
    }
}
