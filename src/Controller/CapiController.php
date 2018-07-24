<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\CoreBundle\Controller;

use Contao\CoreBundle\Capi\ProvidesOpenApiDocs;
use Contao\CoreBundle\Capi\ResourceCollectionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CapiController extends Controller
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

    public function apiAction(Request $request): Response
    {
        if (!$request->attributes->has('_resource')) {
            throw new NotFoundHttpException();
        }

        $resource = $this->resourceCollection->getResourceByFqcn($request->attributes->get('_resource'));

        if (null === $resource) {
            throw new NotFoundHttpException();
        }

        return $resource->getResponse($request);
    }

    public function docsAction(Request $request): Response
    {
        $title = 'Contao API';

        $spec = [
            'openapi' => '3.0.0',
            'info' => [
                'description' => 'This is the Open API documentation for this Contao setup.',
                'title' => $title,
                'version' => 'live',
            ],
            'servers' => [
                ['url' => $request->getHost().'/'.ltrim($this->capiPrefix, '/')],
            ],
            'paths' => [
                '/resource' => [
                    'get' => [
                        'summary' => 'Get a resource.',
                        'responses' => [
                            200 => [
                                'description' => 'OK',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($this->resourceCollection->getResources() as $resource) {
            if ($resource instanceof ProvidesOpenApiDocs) {
                $spec = $resource->getOpenApiDocs($spec);
            }
        }

        return $this->render(
            '@ContaoCore/Capi/docs.html.twig', [
                'title' => $title,
                'spec' => $spec,
            ]
        );
    }
}
