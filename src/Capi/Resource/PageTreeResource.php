<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\CoreBundle\Capi\Resource;

use Contao\CoreBundle\Capi\ResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class PageTreeResource implements ResourceInterface
{
    public function getRoute(): Route
    {
        return (new Route('/pagetree'))->setMethods(['GET']);
    }

    public function getResponse(Request $request): Response
    {
        return new Response('pagetree response');
    }
}
