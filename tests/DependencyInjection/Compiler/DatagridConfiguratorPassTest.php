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
use Rollerworks\Bundle\DatagridBundle\DependencyInjection\Compiler\DatagridConfiguratorPass;
use Rollerworks\Component\Datagrid\DatagridConfiguratorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class DatagridConfiguratorPassTest extends AbstractCompilerPassTestCase
{
    const TAG_NAME = 'rollerworks_datagrid.datagrid_configurator';

    /** @test */
    public function it_registers_datagrid_configurators()
    {
        $configurator1 = $this->createConfigurator();
        $configurator2 = $this->createConfigurator();

        $collectingService = $this->registerService('rollerworks_datagrid.datagrid_registry', 'stdClass')->setArguments([null, []]);

        $this->registerService('acme_user.datagrid.users', $configurator1)->addTag(self::TAG_NAME);
        $this->registerService('acme_user.datagrid.groups', $configurator2)->addTag(self::TAG_NAME);

        $this->compile();

        self::assertNull($collectingService->getArgument(0));
        self::assertEquals(
            [
                $configurator1 => 'acme_user.datagrid.users',
                $configurator2 => 'acme_user.datagrid.groups',
            ],
            $collectingService->getArgument(1)
        );
    }

    /** @test */
    public function it_expects_services_are_public()
    {
        $this->registerService('rollerworks_datagrid.datagrid_registry', 'stdClass')->setArguments([null, []]);

        $this->registerService('acme_user.datagrid.users', $this->createConfigurator())
            ->addTag(self::TAG_NAME)
            ->setPublic(false)
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"acme_user.datagrid.users" must be public as it can be lazy-loaded.');

        $this->compile();
    }

    /** @test */
    public function it_expects_services_are_not_abstract()
    {
        $this->registerService('rollerworks_datagrid.datagrid_registry', 'stdClass')->setArguments([null, []]);

        $this->registerService('acme_user.datagrid.users', $this->createConfigurator())
            ->addTag(self::TAG_NAME)
            ->setAbstract(true)
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"acme_user.datagrid.users" must not be abstract as it can be lazy-loaded.');

        $this->compile();
    }

    /** @test */
    public function it_expects_services_class_implements_DatagridConfiguratorInterface()
    {
        $this->registerService('rollerworks_datagrid.datagrid_registry', 'stdClass')->setArguments([null, []]);

        $this->registerService('acme_user.datagrid.users', 'stdClass')->addTag(self::TAG_NAME);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The class of service "acme_user.datagrid.users" must implement');

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DatagridConfiguratorPass());
    }

    private function createConfigurator(string $className = '')
    {
        return get_class(
            $this->getMockBuilder(DatagridConfiguratorInterface::class)->setMockClassName($className)->getMock()
        );
    }
}
