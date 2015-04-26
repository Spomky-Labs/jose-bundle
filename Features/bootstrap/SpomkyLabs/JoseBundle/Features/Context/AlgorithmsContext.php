<?php

namespace SpomkyLabs\JoseBundle\Features\Context;

/**
 * Behat context class.
 */
trait AlgorithmsContext
{
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
     * @When I list algorithms
     */
    public function iListAlgorithms()
    {
        /*
         * @var \SpomkyLabs\JoseBundle\Service\JoseInterface
         */
        $jose = $this->getContainer()->get('jose');

        //$encrypted = $jose->signAndEncrypt(array("sub"=>"me"), "1234", "ABCD", null, array(),array('alg'=>'RSA1_5', 'enc'=>'A256CBC-HS512'));
        //print_r($encrypted);
        $signed = $jose->sign(array('sub' => 'me'), 'ABCD');
        print_r($signed);
        $jose->load($signed);
    }

    /**
     * @Then I should get a list of algorithms
     */
    public function iShouldGetAListOfAlgorithms()
    {
    }
}
