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

use Prophecy\Argument;
use Rollerworks\Bundle\DatagridBundle\Extension\Symfony\TypeExtension\ActionTypeExtension;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ActionType;
use Rollerworks\Component\Datagrid\PreloadedExtension;
use Rollerworks\Component\Datagrid\Test\ColumnTypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ActionTypeExtensionTest extends ColumnTypeTestCase
{
    protected function getExtensions()
    {
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate('entity_edit', ['id' => 42], Argument::any())->will(
            function ($args) {
                return '/entity/'.$args[1]['id'].'/edit';
            }
        );

        $urlGenerator->generate('entity_edit', ['id' => 42], Argument::any())->will(
            function ($args) {
                return '/entity/'.$args[1]['id'].'/edit';
            }
        );

        $urlGenerator->generate('entity_edit', ['id' => 42, 'foo' => 'bar'], Argument::any())->will(
            function ($args) {
                return '/entity/'.$args[1]['id'].'/edit?foo=bar';
            }
        );

        $urlGenerator->generate('entity_delete', ['id' => 42], Argument::any())->will(
            function ($args) {
                return '/entity/'.$args[1]['id'].'/delete';
            }
        );

        $urlGenerator->generate('entity_list', [], Argument::any())->will(
            function () {
                return '/entity/list';
            }
        );

        $urlGenerator->generate('entity_list', ['filter' => 'something', 'user' => 'sheldon'], Argument::any())->will(
            function () {
                return '/list/?user=sheldon&filter=something';
            }
        );

        $requestStack = new RequestStack();
        $requestStack->push(Request::create('/datagrid'));

        return [
            new PreloadedExtension(
                [],
                [
                    ActionType::class => [
                        new ActionTypeExtension($urlGenerator->reveal(), $requestStack),
                    ],
                ]
            ),
        ];
    }

    protected function getTestedType()
    {
        return ActionType::class;
    }

    public function testPassLabelToHeaderView()
    {
        $column = $this->factory->createColumn(
            'edit',
            $this->getTestedType(),
            [
                'label' => 'My label',
                'data_provider' => function ($data) {
                    return ['key' => $data->key];
                },
                'uri_scheme' => '/entity/{key}/edit',
            ]
        );

        $datagrid = $this->factory->createDatagrid('grid', [$column]);

        $object = new \stdClass();
        $object->key = ' foo ';

        $datagrid->setData([1 => $object]);

        $view = $datagrid->createView();
        $view = $column->createHeaderView($view);

        $this->assertSame('My label', $view->label);
    }

    public function testActionWithAttr()
    {
        $options = [
            'uri_scheme' => '/entity/{key}/edit',
            'redirect_uri' => null,
            'content' => 'edit',
            'attr' => ['class' => 'i-edit'],
            'url_attr' => ['data-new-window' => true],
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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
            'data_provider' => function ($data) {
                return ['key' => $data->key];
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
            'data_provider' => function ($data) {
                return ['key' => $data->key];
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
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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
            'data_provider' => function ($data) {
                return ['key' => $data->key];
            },
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
            'data_provider' => function ($data) {
                return ['key' => $data->key];
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
            'data_provider' => function ($data) {
                return ['id' => $data->id, 'username' => $data->name];
            },
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
            'data_provider' => function ($data) {
                return ['id' => $data->id, 'username' => $data->name];
            },
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
            'data_provider' => function ($data) {
                return ['id' => $data->id, 'username' => $data->name];
            },
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
            'data_provider' => function ($data) {
                return ['id' => $data->id, 'username' => $data->name];
            },
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
            'data_provider' => function ($data) {
                return ['id' => $data->id, 'username' => $data->name];
            },
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
