<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Extension\Symfony;

use Rollerworks\Component\Datagrid\DatagridExtensionInterface;
use Rollerworks\Component\Datagrid\Exception\InvalidArgumentException;
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
    private $typeServiceIds = [];

    /**
     * @var array[]
     */
    private $typeExtensionServiceIds = [];

    /**
     * Constructor.
     *
     * @param ContainerInterface $container               Symfony services container object
     * @param string[]           $typeServiceIds          column-type service-ids (type => service-id )
     * @param array[]            $typeExtensionServiceIds column-type extension service-ids (type => [[service-ids])
     */
    public function __construct(ContainerInterface $container, array $typeServiceIds, array $typeExtensionServiceIds)
    {
        $this->container = $container;
        $this->typeServiceIds = $typeServiceIds;
        $this->typeExtensionServiceIds = $typeExtensionServiceIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($name)
    {
        if (!isset($this->typeServiceIds[$name])) {
            throw new InvalidArgumentException(
                sprintf('The column type "%s" is not registered with the service container.', $name)
            );
        }

        return $this->container->get($this->typeServiceIds[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($name)
    {
        return isset($this->typeServiceIds[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeExtensions($name)
    {
        $extensions = [];

        if (isset($this->typeExtensionServiceIds[$name])) {
            foreach ($this->typeExtensionServiceIds[$name] as $serviceId) {
                $extensions[] = $extension = $this->container->get($serviceId);

                // validate result of getExtendedType() to ensure it is consistent with the service definition
                if ($extension->getExtendedType() !== $name) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'The extended type specified for the service "%s" does not match the actual extended type. '.
                            'Expected "%s", given "%s".',
                            $serviceId,
                            $name,
                            $extension->getExtendedType()
                        )
                    );
                }
            }
        }

        return $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTypeExtensions($name)
    {
        return isset($this->typeExtensionServiceIds[$name]);
    }
}
