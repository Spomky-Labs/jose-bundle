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


/**
 * Behat context trait.
 */
trait KeysAndKeySetsContext
{
    /**
     * Returns HttpKernel service container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected function getContainer();

    /**
     * @param string $service
     * @param string $variable
     * @When I try to get the key :service and store it in the variable :variable
     */
    public function iTryToGetTheKeyAndStoreItInTheVariable($service, $variable)
    {
        $this->$variable = $this->getContainer()->get($service);
    }

    /**
     * @param string $variable
     * @param string $interface
     *
     * @throws \Exception
     *
     * @Then the variable :variable should be an object that implements :interface
     */
    public function theVariableShouldBeAnObjectThatImplements($variable, $interface)
    {
        if (!$this->$variable instanceof $interface) {
            throw new \Exception(sprintf(
                'The variable "%s" is not an instance of "%s". Its class is "%s".',
                $variable,
                $interface,
                get_class($$variable)
            ));
        }
    }
}
