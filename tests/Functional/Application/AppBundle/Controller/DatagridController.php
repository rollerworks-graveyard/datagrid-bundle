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

namespace Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle\Controller;

use Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle\Datagrid\GroupsDatagrid;
use Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle\Datagrid\UsersDatagrid;
use Rollerworks\Component\Datagrid\Extension\Core\Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

final class DatagridController extends Controller
{
    public function datagridAction()
    {
        $factory = $this->get('rollerworks_datagrid.factory');

        $datagrid = $factory->createDatagridBuilder()
            ->add('id', Type\NumberType::class)
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class)
            ->add('regDate', Type\DateTimeType::class, ['format' => 'yyyy-MM-dd HH:mm:ss', 'label' => 'Registered on'])
            ->add(
                'editAction',
                Type\ActionType::class,
                [
                    'uri_scheme' => '#',
                    'redirect_uri' => true,
                    'redirect_route' => null,
                    'data_provider' => function ($data) {
                        return ['id' => $data['id']];
                    },
                ]
            )
            ->getDatagrid('users');

        $datagrid->setData([
            ['id' => 0, 'firstName' => 'Doctor', 'lastName' => 'Who', 'regDate' => new \DateTime('1980-12-05 12:00:00 EST')],
            ['id' => 1, 'firstName' => 'Homer', 'lastName' => 'Simpson', 'regDate' => new \DateTime('1999-12-05 12:00:00 EST')],
            ['id' => 50, 'firstName' => 'Spider', 'lastName' => 'Big', 'regDate' => new \DateTime('2012-08-05 09:12:00 EST')],
        ]);

        return new Response($this->get('twig')->render('@App/users.html.twig', ['datagrid' => $datagrid->createView()]));
    }

    public function datagridByClassAction()
    {
        $factory = $this->get('rollerworks_datagrid.factory');
        $datagrid = $factory->createDatagrid(UsersDatagrid::class);

        $datagrid->setData([
            ['id' => 0, 'firstName' => 'Doctor', 'lastName' => 'Who', 'regDate' => new \DateTime('1980-12-05 12:00:00 EST')],
            ['id' => 1, 'firstName' => 'Homer', 'lastName' => 'Simpson', 'regDate' => new \DateTime('1999-12-05 12:00:00 EST')],
            ['id' => 50, 'firstName' => 'Spider', 'lastName' => 'Big', 'regDate' => new \DateTime('2012-08-05 09:12:00 EST')],
        ]);

        return new Response($this->get('twig')->render('@App/users.html.twig', ['datagrid' => $datagrid->createView()]));
    }

    public function datagridByServiceAction()
    {
        $factory = $this->get('rollerworks_datagrid.factory');
        $datagrid = $factory->createDatagrid(GroupsDatagrid::class);

        $datagrid->setData([
            ['id' => 0, 'firstName' => 'Doctor', 'lastName' => 'Who', 'regDate' => new \DateTime('1980-12-05 12:00:00 EST')],
        ]);

        return new Response($this->get('twig')->render('@App/users.html.twig', ['datagrid' => $datagrid->createView()]));
    }
}
