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
trait LoaderContext
{
    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @Given I try to load the following JWT and I store the result in the variable :variable
     */
    public function iTryToLoadTheFollowingJwtAndIStoreTheResultInTheVariable($variable, PyStringNode $string)
    {
        /**
         * @var $loader \Jose\LoaderInterface
         */
        $loader = $this->getContainer()->get('jose.loader');
        $this->$variable = $loader->load($string->getRaw());
    }

    /**
     * @Then the variable :variable should be an object that implements :interface
     */
    public function theVariableShouldBeAnObjectThatImplements($variable, $interface)
    {
        if (!$this->$variable instanceof $interface) {
            throw new \Exception(sprintf(
                'The variable "%s" is not an instance of "%s". Its class is "%s".',
                $variable,
                $interface,
                get_class($this->$variable)
            ));
        }
    }

    /**
     * @Then the JWS in the variable :variable should contains :number signature
     * @Then the JWS in the variable :variable should contains :number signatures
     */
    public function theJwsInTheVariableShouldContainsSignature($variable, $number)
    {
        $this->theVariableShouldBeAnObjectThatImplements($variable, JWSInterface::class);
        if ((int)$number !== $this->$variable->countSignatures()) {
            throw new \Exception(sprintf(
                'The JWS contains %d signature(s).',
                $this->$variable->countSignatures()
            ));
        }
    }

    /**
     * @Then the JWE in the variable :variable should contains :number recipient
     * @Then the JWE in the variable :variable should contains :number recipients
     */
    public function theJweInTheVariableShouldContainsRecipient($variable, $number)
    {
        $this->theVariableShouldBeAnObjectThatImplements($variable, JWEInterface::class);
        if ((int)$number !== $this->$variable->countRecipients()) {
            throw new \Exception(sprintf(
                'The JWS contains %d recipient(s).',
                $this->$variable->countRecipients()
            ));
        }
    }

    /**
     * @Then the signature :number of the JWS in the variable :variable should be verified using the verifier :verifier and key :key
     */
    public function theSignatureOfTheJwsInTheVariableShouldBeVerifiedUsingTheVerifierAndKey($variable, $verifier, $key, $number)
    {
        /**
         * @var $verifier_service \Jose\VerifierInterface
         */
        $verifier_service = $this->getContainer()->get($verifier);
        /**
         * @var $key_service \Jose\Object\JWKInterface
         */
        $key_service = $this->getContainer()->get($key);

        $verifier_service->verifyWithKey($this->$variable, $key_service, null, $index);

        if ((int)$number !== $index) {
            throw new \Exception(sprintf(
                'The verified signature is at index %d.',
                $index
            ));
        }
    }

    /**
     * @Then the signature :number of the JWS in the variable :variable should be verified using the verifier :verifier and keyset :keyset
     */
    public function theSignatureOfTheJwsInTheVariableShouldBeVerifiedUsingTheVerifierAndKeyset($variable, $verifier, $keyset, $number)
    {
        /**
         * @var $verifier_service \Jose\VerifierInterface
         */
        $verifier_service = $this->getContainer()->get($verifier);
        /**
         * @var $keyset_service \Jose\Object\JWKSetInterface
         */
        $keyset_service = $this->getContainer()->get($keyset);

        $verifier_service->verifyWithKeySet($this->$variable, $keyset_service, null, $index);

        if ((int)$number !== $index) {
            throw new \Exception(sprintf(
                'The verified signature is at index %d.',
                $index
            ));
        }
    }
}
