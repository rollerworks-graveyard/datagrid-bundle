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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Registers the Datagrid column types and type-extension services.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ExtensionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rollerworks_datagrid.extension')) {
            return;
        }

        $definition = $container->getDefinition('rollerworks_datagrid.extension');

        $this->processTypes($container, $definition);
        $this->processTypeExtensions($container, $definition);
    }

    private function processTypes(ContainerBuilder $container, Definition $definition)
    {
        $types = [];

        foreach ($container->findTaggedServiceIds('rollerworks_datagrid.type') as $id => $tag) {
            $def = $container->getDefinition($id);

            if (!$def->isPublic()) {
                throw new InvalidArgumentException(sprintf('The service "%s" must be public as it can be lazy-loaded.', $id));
            }

            if ($def->isAbstract()) {
                throw new InvalidArgumentException(sprintf('The service "%s" must not be abstract as it can be lazy-loaded.', $id));
            }

            $types[$def->getClass()] = $id;
        }

        $definition->replaceArgument(1, $types);
    }

    private function processTypeExtensions(ContainerBuilder $container, Definition $definition)
    {
        $typeExtensions = [];

        foreach ($container->findTaggedServiceIds('rollerworks_datagrid.type_extension') as $id => list($tag)) {
            $def = $container->getDefinition($id);

            if (!$def->isPublic()) {
                throw new InvalidArgumentException(sprintf('The service "%s" must be public as it can be lazy-loaded.', $id));
            }

            if ($def->isAbstract()) {
                throw new InvalidArgumentException(sprintf('The service "%s" must not be abstract as it can be lazy-loaded.', $id));
            }

            if (!isset($tag['extended_type'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Tagged datagrid type extension must have the extended type configured using the '.
                        'extended_type/extended-type attribute, none was configured for the "%s" service.',
                        $id
                    )
                );
            }

            $extendedType = $tag['extended_type'];
            $typeExtensions[$extendedType][] = $id;
        }

        $definition->replaceArgument(2, $typeExtensions);
    }
}
