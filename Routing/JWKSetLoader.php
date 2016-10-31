<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\JoseBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class JWKSetLoader implements LoaderInterface
{
    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $routes;

    /**
     * JWKSetLoader Constructor.
     */
    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    /**
     * @param string $pattern
     * @param string $service
     */
    public function addJWKSetRoute($pattern, $service)
    {
        $json_defaults = ['_controller' => $service.':jsonAction'];
        $json_route = new Route($pattern.'.json', $json_defaults);
        $this->routes->add('jwkset_'.$service.'_json', $json_route);

        $pem_defaults = ['_controller' => $service.':pemAction'];
        $pem_route = new Route($pattern.'.pem', $pem_defaults);
        $this->routes->add('jwkset_'.$service.'_pem', $pem_route);
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'jwkset' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
