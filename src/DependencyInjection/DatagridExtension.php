<?php

namespace Rollerworks\Component\DatagridBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class DatagridExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));

        $loader->load('core.xml');
        $loader->load('type.xml');
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        //return new Configuration($this->getAlias());
    }

    public function getAlias()
    {
        return 'rollerworks_datagrid';
    }
}
