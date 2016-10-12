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

use Rollerworks\Component\Datagrid\DatagridConfiguratorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Registers Datagrid Configurators for loading.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DatagridConfiguratorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rollerworks_datagrid.datagrid_registry')) {
            return;
        }

        $definition = $container->getDefinition('rollerworks_datagrid.datagrid_registry');
        $mapping = [];

        foreach ($container->findTaggedServiceIds('rollerworks_datagrid.datagrid_configurator') as $id => $tag) {
            $def = $container->getDefinition($id);

            if (!$def->isPublic()) {
                throw new InvalidArgumentException(sprintf('The service "%s" must be public as it can be lazy-loaded.', $id));
            }

            if ($def->isAbstract()) {
                throw new InvalidArgumentException(sprintf('The service "%s" must not be abstract as it can be lazy-loaded.', $id));
            }

            if (!in_array(DatagridConfiguratorInterface::class, class_implements($def->getClass()), true)) {
                throw new InvalidArgumentException(sprintf('The class of service "%s" must implement "%s".', $id, DatagridConfiguratorInterface::class));
            }

            $mapping[$def->getClass()] = $id;
        }

        $definition->replaceArgument(1, $mapping);
    }
}
