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
use Jose\Object\EncryptionInstruction;
use Jose\JSONSerializationModes;
use Jose\Object\JWK;
use Jose\Object\SignatureInstruction;

/**
 * Behat context class.
 */
trait EncryptContext
{
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
     * @return \Jose\Object\JWKSetInterface
     */
    abstract protected function getKeyset();

    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

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
