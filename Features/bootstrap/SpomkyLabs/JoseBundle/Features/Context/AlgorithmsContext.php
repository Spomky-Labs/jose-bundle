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
        $this->getContainer()->get("spomky_jose.jwa_manager")->listAlgorithms();
        $this->getContainer()->get("spomky_jose.compression_manager")->listCompressionAlgorithm();
    }

    /**
     * @Then I should get a list of algorithms
     */
    public function iShouldGetAListOfAlgorithms()
    {
    }
}
