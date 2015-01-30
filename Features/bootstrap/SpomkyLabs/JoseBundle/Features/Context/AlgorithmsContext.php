<?php

namespace SpomkyLabs\JoseBundle\Features\Context;

/**
 * Behat context class.
 */
trait AlgorithmsContext
{
    /**
     * @When I list algorithms
     */
    public function iListAlgorithms()
    {
        $result = $this->getContainer()->get("spomky_jose.jwa_manager")->listAlgorithms();
        //var_dump($result);
        $result = $this->getContainer()->get("spomky_jose.compression_manager")->listCompressionAlgorithm();
        //var_dump($result);
    }

    /**
     * @Then I should get a list of algorithms
     */
    public function iShouldGetAListOfAlgorithms()
    {
        //throw new PendingException();
    }
}
