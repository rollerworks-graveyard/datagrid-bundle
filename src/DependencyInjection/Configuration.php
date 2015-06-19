<?php

namespace Rollerworks\Component\DatagridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $configName;

    public function __construct($configName)
    {
        $this->configName = $configName;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->configName);

        $rootNode
            ->children()
                ->arrayNode('twig')
                    ->children()
                        ->arrayNode('themes')
                            ->addDefaultChildrenIfNoneSet()
                            ->prototype('scalar')
                            ->defaultValue(['datagrid.html.twig'])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
