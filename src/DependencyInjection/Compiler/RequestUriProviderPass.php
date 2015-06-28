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

use Rollerworks\Component\Datagrid\Twig\Extension\DatagridExtension as TwigDatagridExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
            $definition->addArgument(new Reference('rollerworks_datagrid.request_uri_provider.request_stack'));

            $container->removeDefinition('rollerworks_datagrid.request_uri_provider.request_service');
            $container->removeDefinition('rollerworks_datagrid.event_subscriber.request');
        } else {
            // Symfony 2.3
            $definition->addArgument(new Reference('rollerworks_datagrid.request_uri_provider.request_service'));

            $container->removeDefinition('rollerworks_datagrid.request_uri_provider.request_stack');
        }
    }
}
