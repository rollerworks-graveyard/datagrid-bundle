<?php

declare(strict_types=1);

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
use Rollerworks\Bundle\DatagridBundle\DependencyInjection\Compiler\ExtensionPass;
use Rollerworks\Bundle\DatagridBundle\Tests\Fixtures\Type;
use Rollerworks\Bundle\DatagridBundle\Tests\Fixtures\TypeExtension;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ColumnType;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionPassTest extends AbstractCompilerPassTestCase
{
    public function testRegisteringOfColumnTypes()
    {
        $this->registerService('rollerworks_datagrid.extension', \stdClass::class)->setArguments([null, [], []]);
        $this->registerService('acme_user.datagrid.type.foo', Type\FooType::class)->addTag('rollerworks_datagrid.type');
        $this->registerService('acme_user.datagrid.type.bar', Type\BarType::class)->addTag('rollerworks_datagrid.type');
        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_datagrid.extension');

        $this->assertNull($collectingService->getArgument(0));
        $this->assertEquals(
            [
                Type\FooType::class => 'acme_user.datagrid.type.foo',
                Type\BarType::class => 'acme_user.datagrid.type.bar',
            ],
            $collectingService->getArgument(1)
        );
        $this->assertEquals([], $collectingService->getArgument(2));
    }

    public function testRegisteringOfColumnTypesExtensions()
    {
        $this->registerService('rollerworks_datagrid.extension', \stdClass::class)->setArguments([null, [], []]);
        $this->registerService('acme_user.datagrid.column_extension.bla', TypeExtension\BlaExtension::class)->addTag(
            'rollerworks_datagrid.type_extension',
            ['extended_type' => ColumnType::class]
        );
        $this->registerService('acme_user.datagrid.column_extension.beep', TypeExtension\BlaExtension::class)->addTag(
            'rollerworks_datagrid.type_extension',
            ['extended_type' => ColumnType::class]
        );
        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_datagrid.extension');

        $this->assertNull($collectingService->getArgument(0));
        $this->assertEquals([], $collectingService->getArgument(1));
        $this->assertEquals(
             [ColumnType::class => ['acme_user.datagrid.column_extension.bla', 'acme_user.datagrid.column_extension.beep']],
             $collectingService->getArgument(2)
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ExtensionPass());
    }
}
