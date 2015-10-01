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
use Jose\JWKSetInterface;

/**
 * Behat context class.
 */
trait ControllerContext
{
    /**
     * @var null|\Jose\JWKSetInterface
     */
    private $jwkset = null;

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
     * @Then The content type is :content_type
     */
    public function theContentTypeIs($content_type)
    {
        $headers = $this->getSession()->getResponseHeaders();
        if (!isset($headers['content-type']) || !is_array($headers['content-type'])) {
            throw new \Exception('The response header does not contain "'.$content_type.'"');
        }
        foreach($headers['content-type'] as $type) {
            if ($content_type === substr($type, 0, strlen($content_type))) {
                return;
            }
        }
        throw new \Exception('The response header does not contain "'.$content_type.'"');
    }

    /**
     * @Then I should see a valid key set
     */
    public function iShouldSeeAValidKeySet()
    {
        /**
         * @var $jwkset_manager \Jose\JWKSetManagerInterface
         */
        $jwkset_manager = $this->getContainer()->get('jose.jwkset_manager');

        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);
        if (!is_array($data)) {
            throw new \Exception('The response is not an array');
        }

        $this->jwkset = $jwkset_manager->createJWKSet($data);
        if (!$this->jwkset instanceof JWKSetInterface) {
            throw new \Exception('The response is not a valid JWKSet');
        }
    }

    /**
     * @Then the key set contains at least one key
     */
    public function theKeySetContainsAtLeastOneKey()
    {
        if (0 === count($this->jwkset)) {
            throw new \Exception('The JWKSet is empty');
        }
    }

}
