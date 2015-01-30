<?php

namespace SpomkyLabs\JoseBundle\Features\Context;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Behat context class.
 */
class FeatureContext extends MinkContext implements SnippetAcceptingContext
{
    use KernelDictionary;
    use AlgorithmsContext;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct()
    {
        AnnotationRegistry::registerAutoloadNamespaces(array(
            'Sensio\\Bundle\\FrameworkExtraBundle' => './vendor/sensio/framework-extra-bundle/',
        ));
    }
}
