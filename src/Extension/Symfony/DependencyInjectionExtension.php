<?php

namespace Rollerworks\Component\DatagridBundle\Extension\Symfony;

use Rollerworks\Component\Datagrid\DatagridExtensionInterface;
use Rollerworks\Component\Datagrid\DatagridInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class DependencyInjectionExtension implements DatagridExtensionInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $columnTypes = [];

    /**
     * @var array[]
     */
    private $columnExtensions = [];

    /**
     * Constructor.
     *
     * @param ContainerInterface $container        Symfony services container object
     * @param string[]           $columnTypes      column-type service-ids (type => service-id )
     * @param array[]            $columnExtensions column-type extension service-ids (type => [[service-ids])
     */
    public function __construct(ContainerInterface $container, array $columnTypes, array $columnExtensions)
    {
        $this->container = $container;
        $this->columnTypes = $columnTypes;
        $this->columnExtensions = $columnExtensions;
    }

    /**
     * Register event listeners.
     *
     * @param DatagridInterface $datagrid
     */
    public function registerListeners(DatagridInterface $datagrid)
    {
        // TODO: Implement registerListeners() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnType($name)
    {
        if (!isset($this->columnTypes[$name])) {
            throw new \InvalidArgumentException(
                sprintf('The field type "%s" is not registered with the service container.', $name)
            );
        }

        $type = $this->container->get($this->columnTypes[$name]);

        if ($type->getName() !== $name) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The type name specified for the service "%s" does not match the actual name.'.
                    'Expected "%s", given "%s"',
                    $this->columnTypes[$name],
                    $name,
                    $type->getName()
                )
            );
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnType($name)
    {
        return isset($this->columnTypes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnTypeExtensions($name)
    {
        $extensions = [];

        if (isset($this->columnExtensions[$name])) {
            foreach ($this->columnExtensions[$name] as $serviceId) {
                $extensions[] = $this->container->get($serviceId);
            }
        }

        return $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColumnTypeExtensions($name)
    {
        return isset($this->columnExtensions[$name]);
    }
}
