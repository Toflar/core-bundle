<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\CoreBundle\Routing;

use Contao\CoreBundle\Capi\ResourceCollectionInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class CapiLoader extends Loader
{
    /**
     * @var ResourceCollectionInterface
     */
    private $resourceCollection;

    /**
     * @var string
     */
    private $capiPrefix;

    public function __construct(ResourceCollectionInterface $resourceCollection, string $capiPrefix)
    {
        $this->resourceCollection = $resourceCollection;
        $this->capiPrefix = $capiPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null): RouteCollection
    {
        $routes = new RouteCollection();

        // Docs endpoint (maybe have that configurable?)
        $docsRoute = new Route('/');
        $docsRoute->setCondition("request.headers.get('Accept') matches '~text/html~'");
        $docsRoute->setDefault('_controller', 'contao.controller.capi::docsAction');
        $docsRoute->setMethods(['GET']);

        $routes->add('contao_capi_docs', $docsRoute);

        foreach ($this->resourceCollection->getResources() as $resource) {
            $resourceFqcn = \get_class($resource);
            $route = $resource->getRoute();
            $route->setDefault('_controller', 'contao.controller.capi::apiAction');
            $route->setDefault('_resource', $resourceFqcn);

            $routes->add('contao_capi_'.str_replace('\\', '_', strtolower($resourceFqcn)), $route);
        }

        $routes->addPrefix($this->capiPrefix);

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null): bool
    {
        return 'contao_capi' === $type;
    }
}
