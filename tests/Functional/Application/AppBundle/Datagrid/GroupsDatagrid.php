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

namespace Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle\Datagrid;

use Rollerworks\Component\Datagrid\DatagridBuilderInterface;
use Rollerworks\Component\Datagrid\DatagridConfiguratorInterface;
use Rollerworks\Component\Datagrid\Extension\Core\Type;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class GroupsDatagrid implements DatagridConfiguratorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    // Require a DI injection to ensure it's loaded as a service.
    // and not as the FQCN construction.
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildDatagrid(DatagridBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Type\NumberType::class)
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class)
            ->add('regDate', Type\DateTimeType::class, ['format' => 'yyyy-MM-dd HH:mm:ss', 'label' => 'Registered on'])
            ->createCompound(
                'actions',
                [
                    'data_provider' => function ($data) {
                        return ['id' => $data['id']];
                    },
                ])
                ->add(
                    'edit',
                    Type\ActionType::class,
                    [
                        'route_name' => 'search',
                    ]
                )
            ->end()
        ;
    }
}
