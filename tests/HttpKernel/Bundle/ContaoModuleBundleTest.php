<?php

/**
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Contao\CoreBundle\Test\HttpKernel\Bundle;

use Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle;
use Contao\CoreBundle\Test\TestCase;

/**
 * Tests the ContaoModuleBundle class.
 *
 * @author Leo Feyer <https://contao.org>
 */
class ContaoModuleBundleTest extends TestCase
{
    /**
     * @var ContaoModuleBundle
     */
    protected $bundle;

    /**
     * Creates a new Contao module bundle.
     */
    protected function setUp()
    {
        $this->bundle = new ContaoModuleBundle('legacy-module', $this->getRootDir() . '/app');
    }

    /**
     * Tests the object instantiation.
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf('Contao\CoreBundle\HttpKernel\Bundle\ContaoModuleBundle', $this->bundle);
    }

    /**
     * Tests the getPublicFolders() method.
     */
    public function testGetPublicFolders()
    {
        $this->assertContains(
            $this->getRootDir() . '/system/modules/legacy-module/assets',
            $this->bundle->getPublicFolders()
        );

        $this->assertContains(
            $this->getRootDir() . '/system/modules/legacy-module/html',
            $this->bundle->getPublicFolders()
        );

        $this->assertNotContains(
            $this->getRootDir() . '/system/modules/legacy-module/private',
            $this->bundle->getPublicFolders()
        );
    }

    /**
     * Tests the getContaoResourcesPath() method.
     */
    public function testGetContaoResourcesPath()
    {
        $this->assertEquals(
            $this->getRootDir() . '/system/modules/legacy-module',
            $this->bundle->getContaoResourcesPath()
        );
    }

    /**
     * Tests the getPath() method.
     */
    public function testGetPath()
    {
        $this->assertEquals(
            $this->getRootDir() . '/system/modules/legacy-module',
            $this->bundle->getPath()
        );
    }
}
