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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds all services with the tags "rollerworks_datagrid.type" and "rollerworks_datagrid.type_extension" as
 * arguments of the "rollerworks_datagrid.extension" service.
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

        // Builds an array with fully-qualified type class names as keys and service IDs as values
        $types = [];

        foreach ($container->findTaggedServiceIds('rollerworks_datagrid.type') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new \InvalidArgumentException(
                    sprintf('The service "%s" must be public as datagrid types are lazy-loaded.', $serviceId)
                );
            }

            // Support type access by FQCN
            $types[$serviceDefinition->getClass()] = $serviceId;
        }

        $definition->replaceArgument(1, $types);

        $typeExtensions = [];

        foreach ($container->findTaggedServiceIds('rollerworks_datagrid.type_extension') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new \InvalidArgumentException(
                    sprintf('The service "%s" must be public as datagrid type extensions are lazy-loaded.', $serviceId)
                );
            }

            if (isset($tag[0]['extended_type'])) {
                $extendedType = $tag[0]['extended_type'];
            } else {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Tagged datagrid type extension must have the extended type configured using the '.
                        'extended_type/extended-type attribute, none was configured for the "%s" service.',
                        $serviceId
                    )
                );
            }

            $typeExtensions[$extendedType][] = $serviceId;
        }

        $definition->replaceArgument(2, $typeExtensions);
    }
}
