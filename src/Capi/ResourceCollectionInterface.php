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

interface ResourceCollectionInterface
{
    /**
     * @return ResourceInterface[]
     */
    public function getResources(): array;

    public function getResourceByFqcn(string $fqcn): ?ResourceInterface;

    public function addResource(ResourceInterface $resource): self;
}
