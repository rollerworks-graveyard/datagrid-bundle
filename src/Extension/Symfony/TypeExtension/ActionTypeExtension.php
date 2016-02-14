<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Extension\Symfony\TypeExtension;

use Rollerworks\Component\Datagrid\Column\AbstractTypeExtension;
use Rollerworks\Component\Datagrid\Extension\Core\Type\ActionType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ActionTypeExtension extends AbstractTypeExtension
{
    /**
     * Router to generate urls.
     *
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * RequestStack.
     *
     * This is used for getting the current URI for redirects.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $router
     * @param RequestStack          $requestStack
     */
    public function __construct(UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'route_name' => null,
                'parameters_field_mapping' => [],
                'additional_parameters' => [],
                'uri_scheme' => function (Options $options, $value) {
                    // Value was already provided so just use that one.
                    // Always do this check as lazy options don't overwrite.
                    if (is_string($value)) {
                        return $value;
                    }

                    if (null !== $options['route_name']) {
                        return $this->createRouteGenerator(
                            $options['route_name'],
                            $options['reference_type'],
                            $options['parameters_field_mapping'],
                            $options['additional_parameters']
                        );
                    }
                },
                'reference_type' => UrlGeneratorInterface::ABSOLUTE_PATH,

                'redirect_route' => null,
                'redirect_parameters_field_mapping' => [],
                'redirect_additional_parameters' => [],
                'redirect_uri' => function (Options $options, $value) {
                    // Value was already provided so just use that one.
                    // Always do this check as lazy options don't overwrite.
                    if (is_string($value)) {
                        return $value;
                    }

                    if (null !== $options['redirect_route']) {
                        return $this->createRouteGenerator(
                            $options['redirect_route'],
                            $options['reference_type'],
                            $options['redirect_parameters_field_mapping'],
                            $options['redirect_additional_parameters']
                        );
                    }

                    return $this->requestStack->getMasterRequest()->getRequestUri();
                },
            ]
        );

        $resolver->setAllowedTypes('route_name', ['string', 'null']);
        $resolver->setAllowedTypes('parameters_field_mapping', ['array']);
        $resolver->setAllowedTypes('additional_parameters', ['array']);

        $resolver->setAllowedTypes('redirect_route', ['string', 'null']);
        $resolver->setAllowedTypes('redirect_parameters_field_mapping', ['array']);
        $resolver->setAllowedTypes('redirect_additional_parameters', ['array']);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ActionType::class;
    }

    private function createRouteGenerator($routeName, $referenceType, array $fieldMapping, array $additionalParameters)
    {
        return function (array $values) use ($routeName, $referenceType, $fieldMapping, $additionalParameters) {
            $routeParameters = [];

            foreach ($fieldMapping as $parameterName => $mappingField) {
                $routeParameters[$parameterName] = $values[$mappingField];
            }

            return $this->router->generate(
                $routeName,
                array_merge($routeParameters, $additionalParameters),
                $referenceType
            );
        };
    }
}
