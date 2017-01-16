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

use Rollerworks\Component\Datagrid\Twig\Extension\DatagridExtension as TwigDatagridExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers the Datagrid base themes for loading.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class TwigRenderEnginePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definitionName = 'twig.loader.filesystem';

        if (!$container->hasDefinition('twig.loader.filesystem')) {
            if (!$container->hasDefinition('twig.loader.native_filesystem')) {
                return;
            }

            $definitionName = 'twig.loader.native_filesystem';
        }

        $reflection = new \ReflectionClass(TwigDatagridExtension::class);
        $extensionFolder = dirname(dirname(dirname($reflection->getFileName())));

        $container->getDefinition($definitionName)->addMethodCall(
            'addPath',
            [$extensionFolder.'/Resources/theme']
        );
    }
}
