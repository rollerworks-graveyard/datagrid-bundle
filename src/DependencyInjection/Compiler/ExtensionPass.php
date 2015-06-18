<?php

namespace Rollerworks\Component\DatagridBundle\DependencyInjection\Compiler;

use Rollerworks\Component\Datagrid\Twig\Extension\DatagridExtension as TwigDatagridExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds all services with the tags "rollerworks_datagrid.column_type" and "rollerworks_datagrid.column_extension" as
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

        $this->processTwig($container);
        $this->processExtensions($container);
        $this->processTypes($definition, $container);
        $this->processTypeExtensions($definition, $container);
    }

    private function processTwig(ContainerBuilder $container)
    {
        $container->getDefinition('twig.loader.filesystem')->addMethodCall(
            'addPath',
            [dirname((new \ReflectionClass(TwigDatagridExtension::class))->getFileName().'/../').'/Resources/views/theme']
        );
    }

    private function processExtensions(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rollerworks_datagrid.registry')) {
            return;
        }

        $definition = $container->getDefinition('rollerworks_datagrid.registry');
        $extensions = $definition->getArgument(0);

        foreach (array_keys($container->findTaggedServiceIds('rollerworks_datagrid.extension')) as $serviceId) {
            $extensions[] = new Reference($serviceId);
        }

        $definition->replaceArgument(0, $extensions);
    }

    private function processTypes(Definition $definition, ContainerBuilder $container)
    {
        $types = array();

        foreach ($container->findTaggedServiceIds('rollerworks_datagrid.column_type') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias']) ? $tag[0]['alias'] : $serviceId;
            // Flip, because we want tag aliases (= type identifiers) as keys
            $types[$alias] = $serviceId;
        }

        $definition->replaceArgument(1, $types);
    }

    private function processTypeExtensions(Definition $definition, ContainerBuilder $container)
    {
        $typeExtensions = array();

        foreach ($container->findTaggedServiceIds('rollerworks_datagrid.column_extension') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias']) ? $tag[0]['alias'] : $serviceId;
            $typeExtensions[$alias][] = $serviceId;
        }

        $definition->replaceArgument(2, $typeExtensions);
    }
}
