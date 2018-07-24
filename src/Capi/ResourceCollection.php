<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\CoreBundle\Capi;

class ResourceCollection implements ResourceCollectionInterface
{
    /**
     * @var array ResourceInterface[]
     */
    private $resources = [];

    public function __construct(iterable $resources)
    {
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    /**
     * @return ResourceInterface[]
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    public function getResourceByFqcn(string $fqcn): ?ResourceInterface
    {
        return $this->resources[$fqcn];
    }

    public function addResource(ResourceInterface $resource): ResourceCollectionInterface
    {
        $this->resources[\get_class($resource)] = $resource;

        return $this;
    }
}
