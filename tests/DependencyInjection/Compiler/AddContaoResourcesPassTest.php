<?php

/**
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Test\DependencyInjection\Compiler;

use Contao\CoreBundle\DependencyInjection\Compiler\AddContaoResourcesPass;
use Contao\CoreBundle\Test\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Tests the AddContaoResourcesPassTest class.
 *
 * @author Andreas Schempp <http://github.com/aschempp>
 */
class AddContaoResourcesPassTest extends TestCase
{
    /**
     * Tests the object instantiation.
     */
    public function testInstantiation()
    {
        $pass = new AddContaoResourcesPass('');

        $this->assertInstanceOf('Contao\CoreBundle\DependencyInjection\Compiler\AddContaoResourcesPass', $pass);
    }

    /**
     * Tests processing the pass without definition.
     */
    public function testWithoutDefinition()
    {
        $pass = new AddContaoResourcesPass('');

        $pass->process(new ContainerBuilder());
    }

    /**
     * Tests adding resources.
     */
    public function testAddResources()
    {
        $container = $this->getContainerBuilder();
        $pass      = new AddContaoResourcesPass('testPath');

        $pass->process($container);

        $definition = $container->getDefinition('contao.resource_provider');
        $calls      = $definition->getMethodCalls();

        $this->assertEquals('addResourcesPath', $calls[0][0]);
        $this->assertEquals('testPath', $calls[0][1][0]);
    }

    /**
     * Tests adding public folders.
     */
    public function testAddPublicFolders()
    {
        $container = $this->getContainerBuilder();
        $pass      = new AddContaoResourcesPass('testPath', ['publicFolder1']);

        $pass->process($container);

        $definition = $container->getDefinition('contao.resource_provider');
        $calls      = $definition->getMethodCalls();

        $this->assertEquals('addPublicFolders', $calls[1][0]);
        $this->assertEquals(['publicFolder1'], $calls[1][1][0]);
    }

    /**
     * Returns a container builder with the contao.resource_provider service.
     *
     * @return ContainerBuilder The container builder instance
     */
    private function getContainerBuilder()
    {
        $container = new ContainerBuilder();

        $container->setDefinition(
            'contao.resource_provider',
            new Definition('Contao\\CoreBundle\\HttpKernel\\Bundle\\ResourceProvider')
        );

        return $container;
    }
}