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

final class UsersDatagrid implements DatagridConfiguratorInterface
{
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
                        'redirect_uri' => true,
                        'route_name' => 'search',
                    ]
                )
            ->end()
        ;
    }
}
