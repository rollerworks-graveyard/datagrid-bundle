<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Rollerworks\Bundle\DatagridBundle\DependencyInjection\Configuration;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    public function testDefaultsAreValid()
    {
        $this->assertProcessedConfigurationEquals(
            [
                [],
            ],
            [
                'twig' => [
                    'themes' => ['datagrid.html.twig'],
                ],
            ]
        );
    }

    protected function getConfiguration()
    {
        return new Configuration('datagrid');
    }
}
