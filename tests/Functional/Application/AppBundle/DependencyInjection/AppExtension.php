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

namespace Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle\DependencyInjection;

use Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle\Datagrid\GroupsDatagrid;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class AppExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $container->register('app.datagrid.groups', GroupsDatagrid::class)
            ->addArgument(new Reference('service_container'))
            ->addTag('rollerworks_datagrid.datagrid_configurator')
        ;
    }
}
