<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle\Controller;

use Rollerworks\Component\Datagrid\Extension\Core\Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

final class DatagridController extends Controller
{
    public function datagridAction()
    {
        $factory = $this->get('rollerworks_datagrid.factory');

        $datagrid = $factory->createDatagridBuilder('users')
            ->add('id', Type\NumberType::class)
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class)
            ->add('regDate', Type\DateTimeType::class, ['format' => 'yyyy-MM-dd HH:mm:ss', 'label' => 'Registered on'])
            ->getDatagrid();

        $datagrid->setData([
            ['id' => 0, 'firstName' => 'Doctor', 'lastName' => 'Who', 'regDate' => new \DateTime('1980-12-05 12:00:00 EST')],
            ['id' => 1, 'firstName' => 'Homer', 'lastName' => 'Simpson', 'regDate' => new \DateTime('1999-12-05 12:00:00 EST')],
            ['id' => 50, 'firstName' => 'Spider', 'lastName' => 'Big', 'regDate' => new \DateTime('2012-08-05 09:12:00 EST')],
        ]);

        return $this->render('AppBundle::users.html.twig', ['datagrid' => $datagrid->createView() ]);
    }
}
