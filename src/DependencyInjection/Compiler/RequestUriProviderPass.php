<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class RequestUriProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rollerworks_datagrid.column_extension.action')) {
            return;
        }

        $definition = $container->getDefinition('rollerworks_datagrid.column_extension.action');

        // Symfony >=2.4
        if ($container->hasDefinition('request_stack') || $container->hasAlias('request_stack')) {
            $container->setAlias('rollerworks_datagrid.request_uri_provider', new Alias('rollerworks_datagrid.request_uri_provider.request_stack', false));
        } else {
            // Symfony 2.3
            $container->setAlias('rollerworks_datagrid.request_uri_provider', new Alias('rollerworks_datagrid.request_uri_provider.request_service', false));
        }
    }
}
