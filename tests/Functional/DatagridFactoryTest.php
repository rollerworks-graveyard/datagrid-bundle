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

namespace Rollerworks\Bundle\DatagridBundle\Tests\Functional;

final class DatagridFactoryTest extends FunctionalTestCase
{
    public function testDatagridWorks()
    {
        $client = self::newClient();
        $client->request('GET', '/datagrid');

        $this->assertHtmlEquals(
            <<<'HTML'
<table>
    <thead>
    <tr>
        <th><span>Id</span></th>
        <th><span>First name</span></th>
        <th><span>Last name</span></th>
        <th><span>Registered on</span></th>
        <th><span>Edit action</span></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <div>0</div>
        </td>
        <td>
            <div>Doctor</div>
        </td>
        <td>
            <div>Who</div>
        </td>
        <td>
            <div>1980-12-05 17:00:00</div>
        </td>
        <td><a href="#?redirect_uri=%2Fdatagrid">Edit action</a></td>
    </tr>
    <tr>
        <td>
            <div>1</div>
        </td>
        <td>
            <div>Homer</div>
        </td>
        <td>
            <div>Simpson</div>
        </td>
        <td>
            <div>1999-12-05 17:00:00</div>
        </td>
        <td><a href="#?redirect_uri=%2Fdatagrid">Edit action</a></td>
    </tr>
    <tr>
        <td>
            <div>50</div>
        </td>
        <td>
            <div>Spider</div>
        </td>
        <td>
            <div>Big</div>
        </td>
        <td>
            <div>2012-08-05 14:12:00</div>
        </td>
        <td><a href="#?redirect_uri=%2Fdatagrid">Edit action</a></td>
    </tr>
    </tbody>
</table>
HTML
, $client->getResponse()->getContent());
    }

    public function testDatagridByFQCNWorks()
    {
        $client = self::newClient();
        $client->request('GET', '/datagrid-by-class');

        $this->assertHtmlEquals(
            <<<'HTML'
<table>
        <thead>
            <tr>
                <th><span>Actions</span></th>
                <th><span>Id</span></th>
                <th><span>First name</span></th>
                <th><span>Last name</span></th>
                <th><span>Registered on</span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="/datagrid?id=0&redirect_uri=%2Fdatagrid-by-class">Edit</a></td>
                <td>
                    <div>0</div>
                </td>
                <td>
                    <div>Doctor</div>
                </td>
                <td>
                    <div>Who</div>
                </td>
                <td>
                    <div>1980-12-05 17:00:00</div>
                </td>
            </tr>
            <tr>
                <td><a href="/datagrid?id=1&redirect_uri=%2Fdatagrid-by-class">Edit</a></td>
                <td>
                    <div>1</div>
                </td>
                <td>
                    <div>Homer</div>
                </td>
                <td>
                    <div>Simpson</div>
                </td>
                <td>
                    <div>1999-12-05 17:00:00</div>
                </td>
            </tr>
            <tr>
                <td><a href="/datagrid?id=50&redirect_uri=%2Fdatagrid-by-class">Edit</a></td>
                <td>
                    <div>50</div>
                </td>
                <td>
                    <div>Spider</div>
                </td>
                <td>
                    <div>Big</div>
                </td>
                <td>
                    <div>2012-08-05 14:12:00</div>
                </td>
            </tr>
        </tbody>
    </table>
HTML
, $client->getResponse()->getContent());
    }

    public function testDatagridByServiceWorks()
    {
        $client = self::newClient();
        $client->request('GET', '/datagrid-by-service');

        $this->assertHtmlEquals(
            <<<'HTML'
<table>
        <thead>
            <tr>
                <th><span>Actions</span></th>
                <th><span>Id</span></th>
                <th><span>First name</span></th>
                <th><span>Last name</span></th>
                <th><span>Registered on</span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="/datagrid?id=0">Edit</a></td>
                <td>
                    <div>0</div>
                </td>
                <td>
                    <div>Doctor</div>
                </td>
                <td>
                    <div>Who</div>
                </td>
                <td>
                    <div>1980-12-05 17:00:00</div>
                </td>
            </tr>
        </tbody>
    </table>
HTML
, $client->getResponse()->getContent());
    }

    private function assertHtmlEquals($expected, $outputHtml)
    {
        $this->assertSame(
            $this->normalizeWhitespace($expected),
            $this->normalizeWhitespace($outputHtml)
        );
    }

    private function normalizeWhitespace($value)
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = preg_replace(['/\s+/', '/>\s*</'], [' ', '><'], $value);
        $value = trim($value);

        return $value;
    }
}
