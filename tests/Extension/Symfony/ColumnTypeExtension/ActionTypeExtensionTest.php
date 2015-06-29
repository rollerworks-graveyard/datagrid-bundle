<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Tests\Extension\Symfony\ColumnTypeExtension;

use Rollerworks\Bundle\DatagridBundle\Extension\Symfony\ColumnTypeExtension\ActionTypeExtension;
use Rollerworks\Bundle\DatagridBundle\Extension\Symfony\RequestUriProviderInterface;
use Rollerworks\Component\Datagrid\PreloadedExtension;
use Rollerworks\Component\Datagrid\Test\ColumnTypeTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ActionTypeExtensionTest extends ColumnTypeTestCase
{
    protected function getExtensions()
    {
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate('entity_edit', ['id' => 42], false)->will(
            function ($args) {
                return '/entity/'.$args[1]['id'].'/edit';
            }
        );

        $urlGenerator->generate('entity_edit', ['id' => 42], false)->will(
            function ($args) {
                return '/entity/'.$args[1]['id'].'/edit';
            }
        );

        $urlGenerator->generate('entity_edit', ['id' => 42, 'foo' => 'bar'], false)->will(
            function ($args) {
                return '/entity/'.$args[1]['id'].'/edit?foo=bar';
            }
        );

        $urlGenerator->generate('entity_delete', ['id' => 42], false)->will(
            function ($args) {
                return '/entity/'.$args[1]['id'].'/delete';
            }
        );

        $urlGenerator->generate('entity_list', [], false)->will(
            function () {
                return '/entity/list';
            }
        );

        $urlGenerator->generate('entity_list', ['filter' => 'something', 'user' => 'sheldon'], false)->will(
            function () {
                return '/list/?user=sheldon&filter=something';
            }
        );

        $requestUriProvider = $this->prophesize(RequestUriProviderInterface::class);
        $requestUriProvider->getRequestUri()->willReturn('/datagrid');

        return [
            new PreloadedExtension(
                [],
                [
                    'action' => [
                        new ActionTypeExtension($urlGenerator->reveal(), $requestUriProvider->reveal()),
                    ],
                ]
            ),
        ];
    }

    protected function getTestedType()
    {
        return 'action';
    }

    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn(
            'edit',
            $this->getTestedType(),
            $this->datagrid,
            [
                'content' => 'My label',
                'field_mapping' => ['key'],
                'uri_scheme' => '/entity/{key}/edit',
            ]
        );

        $object = new \stdClass();
        $object->key = ' foo ';
        $this->datagrid->setData([1 => $object]);

        $datagridView = $this->datagrid->createView();
        $view = $column->createHeaderView($datagridView);

        $this->assertEquals('My label', $view->label);
    }

    public function testActionWithAttr()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'redirect_uri' => null,
            'content' => 'edit',
            'attr' => ['class' => 'i-edit'],
            'url_attr' => ['data-new-window' => true],
        ];

        $expectedAttributesAttributes = [
            'url' => '/entity/42/edit',
            'content' => 'edit',
            'attr' => ['class' => 'i-edit'],
            'url_attr' => ['data-new-window' => true],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributesAttributes);
    }

    public function testActionWithUriAsClosure()
    {
        $options = [
            'content' => 'Delete',
            'redirect_uri' => null,
            'uri_scheme' => function ($values) {
                return '/entity/'.$values['key'].'/delete';
            },
        ];

        $expectedAttributes = [
            'url' => '/entity/42/delete',
            'content' => 'Delete',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionWithContentAsClosure()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/delete',
            'redirect_uri' => null,
            'content' => function ($values) {
                return 'Delete #'.$values['key'];
            },
        ];

        $expectedAttributes = [
            'content' => 'Delete #42',
            'url' => '/entity/42/delete',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionWithRedirectUri()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'content' => 'edit',
            'redirect_uri' => '/entity/list',
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?redirect_uri=%2Fentity%2Flist',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionWithRedirectUriWithExistingQueryStringInUrl()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit?foo=bar',
            'content' => 'delete',
            'redirect_uri' => '/entity/list?filter=something',
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?foo=bar&redirect_uri=%2Fentity%2Flist%3Ffilter%3Dsomething',
            'content' => 'delete',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionWithRedirectUriAsClosure()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'content' => 'edit',
            'redirect_uri' => function ($values) {
                return '/entity/list/?last-entity='.$values['key'];
            },
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?redirect_uri=%2Fentity%2Flist%2F%3Flast-entity%3D42',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $this->assertCellValueEquals(['key' => 42], 42, $options, $expectedAttributes);
    }

    public function testActionWithMultipleFields()
    {
        $options = [
            'uri_scheme' => '/entity/{id}/edit?name={username}',
            'redirect_uri' => null,
            'content' => 'edit',
            'field_mapping' => ['id' => 'id', 'username' => 'name'],
        ];

        $expectedAttributes = [
            'url' => '/entity/50/edit?name=sheldon',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $object = new \stdClass();
        $object->id = 50;
        $object->name = 'sheldon';

        $data = [1 => $object];

        $this->assertCellValueEquals(['id' => 50, 'username' => 'sheldon'], $data, $options, $expectedAttributes);
    }

    // ---

    public function testActionWithRouteName()
    {
        $options = [
            'route_name' => 'entity_edit',
            'parameters_field_mapping' => ['id' => 'id'],
            'content' => 'edit',
            'field_mapping' => ['id' => 'id', 'username' => 'name'],
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?redirect_uri=%2Fdatagrid',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $object = new \stdClass();
        $object->id = 42;
        $object->name = 'sheldon';

        $data = [1 => $object];

        $this->assertCellValueEquals(['id' => 42, 'username' => 'sheldon'], $data, $options, $expectedAttributes);
    }

    public function testActionWithAdditionalParams()
    {
        $options = [
            'route_name' => 'entity_edit',
            'parameters_field_mapping' => ['id' => 'id'],
            'additional_parameters' => ['foo' => 'bar'],
            'content' => 'edit',
            'field_mapping' => ['id' => 'id', 'username' => 'name'],
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?foo=bar&redirect_uri=%2Fdatagrid',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $object = new \stdClass();
        $object->id = 42;
        $object->name = 'sheldon';

        $data = [1 => $object];

        $this->assertCellValueEquals(['id' => 42, 'username' => 'sheldon'], $data, $options, $expectedAttributes);
    }

    public function testActionWithRedirectRouteName()
    {
        $options = [
            'route_name' => 'entity_edit',
            'redirect_route' => 'entity_list',
            'parameters_field_mapping' => ['id' => 'id'],
            'content' => 'edit',
            'field_mapping' => ['id' => 'id', 'username' => 'name'],
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?redirect_uri=%2Fentity%2Flist',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $object = new \stdClass();
        $object->id = 42;
        $object->name = 'sheldon';

        $data = [1 => $object];

        $this->assertCellValueEquals(['id' => 42, 'username' => 'sheldon'], $data, $options, $expectedAttributes);
    }

    public function testActionWithRedirectAdditionalParams()
    {
        $options = [
            'route_name' => 'entity_edit',
            'parameters_field_mapping' => ['id' => 'id'],
            'additional_parameters' => ['foo' => 'bar'],

            'redirect_route' => 'entity_list',
            'redirect_parameters_field_mapping' => ['user' => 'username'],
            'redirect_additional_parameters' => ['filter' => 'something'],

            'content' => 'edit',
            'field_mapping' => ['id' => 'id', 'username' => 'name'],
        ];

        $expectedAttributes = [
            'url' => '/entity/42/edit?foo=bar&redirect_uri=%2Flist%2F%3Fuser%3Dsheldon%26filter%3Dsomething',
            'content' => 'edit',
            'attr' => [],
            'url_attr' => [],
        ];

        $object = new \stdClass();
        $object->id = 42;
        $object->name = 'sheldon';

        $data = [1 => $object];

        $this->assertCellValueEquals(['id' => 42, 'username' => 'sheldon'], $data, $options, $expectedAttributes);
    }
}
