<?php

namespace SpomkyLabs\JoseBundle\Features\Context;

use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Behat context class.
 */
class FeatureContext extends MinkContext implements SnippetAcceptingContext
{
    use KernelDictionary;
    use AlgorithmsContext;
}
