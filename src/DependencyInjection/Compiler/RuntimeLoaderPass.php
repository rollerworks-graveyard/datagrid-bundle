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

namespace Rollerworks\Bundle\DatagridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers Twig runtime service (for Symfony <3.2).
 */
final class RuntimeLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Extension already registered or Twig is not enabled at all.
        if ($container->hasDefinition('twig.runtime_loader') || !$container->hasDefinition('twig')) {
            return;
        }

        $definition = $container->getDefinition('twig');
        $definition->addMethodCall('addRuntimeLoader', [new Reference('rollerworks_datagrid.twig.runtime_loader')]);
    }
}
