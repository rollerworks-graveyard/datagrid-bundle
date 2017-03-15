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

namespace Rollerworks\Bundle\DatagridBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Rollerworks\Bundle\DatagridBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

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
