<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Rollerworks\Bundle\DatagridBundle\DependencyInjection\Compiler\RequestUriProviderPass;
use Rollerworks\Bundle\DatagridBundle\DependencyInjection\DatagridExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RequestUriProviderPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->container->registerExtension(new DatagridExtension());
        $this->container->loadFromExtension('rollerworks_datagrid');
    }

    public function testSymfony23RequestListener()
    {
        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'rollerworks_datagrid.request_uri_provider',
            'rollerworks_datagrid.request_uri_provider.request_service'
        );
    }

    public function testSymfony24AndHigherRequestStack()
    {
        $this->registerService('request_stack', 'stdClass');
        $this->compile();

        $this->assertContainerBuilderHasAlias(
            'rollerworks_datagrid.request_uri_provider',
            'rollerworks_datagrid.request_uri_provider.request_stack'
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RequestUriProviderPass());
    }
}
