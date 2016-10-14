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

namespace Rollerworks\Bundle\DatagridBundle\Extension\Symfony;

use Rollerworks\Component\Datagrid\DatagridConfiguratorInterface;
use Rollerworks\Component\Datagrid\DatagridRegistryInterface;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Loads datagrid configurators using the Symfony service-container.
 *
 * @internal
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
final class ContainerDatagridRegistry implements DatagridRegistryInterface
{
    private $container;
    private $configurators;
    private $configuratorInstances = [];

    public function __construct(ContainerInterface $container, array $configurators)
    {
        $this->container = $container;
        $this->configurators = $configurators;
    }

    /**
     * Returns a DatagridConfigurator by name.
     *
     * @param string $name The name of the datagrid configurator
     *
     * @throws InvalidArgumentException if the configurator can not be retrieved
     *
     * @return DatagridConfiguratorInterface
     */
    public function getConfigurator(string $name): DatagridConfiguratorInterface
    {
        if (isset($this->configurators[$name])) {
            return $this->container->get($this->configurators[$name]);
        }

        if (isset($this->configuratorInstances[$name])) {
            return $this->configuratorInstances[$name];
        }

        // Support fully-qualified class names.
        if (class_exists($name) && in_array(DatagridConfiguratorInterface::class, class_implements($name), true)) {
            return $this->configuratorInstances[$name] = new $name();
        }

        throw new InvalidArgumentException(sprintf('Could not load datagrid configurator "%s"', $name));
    }

    /**
     * Returns whether the given DatagridConfigurator is supported.
     *
     * @param string $name The name of the datagrid configurator
     *
     * @return bool
     */
    public function hasConfigurator(string $name): bool
    {
        if (isset($this->configurators[$name]) || isset($this->configuratorInstances[$name])) {
            return true;
        }

        return class_exists($name) && in_array(DatagridConfiguratorInterface::class, class_implements($name), true);
    }
}
