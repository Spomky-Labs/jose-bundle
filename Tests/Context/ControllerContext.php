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

use Jose\Object\JWKSetInterface;
use Jose\Object\JWKSet;

/**
 * Behat context class.
 */
trait ControllerContext
{
    private $controller_response;

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
        foreach ($headers['content-type'] as $type) {
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
        $content = $this->getSession()->getPage()->getContent();
        $data = json_decode($content, true);
        if (!is_array($data)) {
            throw new \Exception('The response is not an array');
        }

        $this->controller_response = new JWKSet($data);
        if (!$this->controller_response instanceof JWKSetInterface) {
            throw new \Exception('The response is not a valid JWKSet');
        }
    }

    /**
     * @Then the response contains at least one key
     */
    public function theResponseContainsAtLeastOneKey()
    {
        if (0 === count($this->controller_response)) {
            throw new \Exception('The JWKSet is empty');
        }
    }
}
