<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Extension\Symfony\ColumnTypeExtension;

use Rollerworks\Bundle\DatagridBundle\Extension\Symfony\RequestUriProviderInterface;
use Rollerworks\Component\Datagrid\Column\AbstractColumnTypeExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ActionTypeExtension extends AbstractColumnTypeExtension
{
    /**
     * Router to generate urls.
     *
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * RequestUriProvider.
     *
     * This is used for getting the current URI for redirects.
     *
     * @var RequestUriProviderInterface
     */
    private $requestUriProvider;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface       $router
     * @param RequestUriProviderInterface $requestUriProvider
     */
    public function __construct(UrlGeneratorInterface $router, RequestUriProviderInterface $requestUriProvider)
    {
        $this->router = $router;
        $this->requestUriProvider = $requestUriProvider;
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

                    return $this->requestUriProvider->getRequestUri();
                },
            ]
        );

        if ($resolver instanceof OptionsResolverInterface) {
            $resolver->setAllowedTypes(
                [
                    'route_name' => ['string', 'null'],
                    'parameters_field_mapping' => ['array'],
                    'additional_parameters' => ['array'],

                    'redirect_route' => ['string', 'null'],
                    'redirect_parameters_field_mapping' => ['array'],
                    'redirect_additional_parameters' => ['array'],
                ]
            );
        } else {
            $resolver->setAllowedTypes('route_name', ['string', 'null']);
            $resolver->setAllowedTypes('parameters_field_mapping', ['array']);
            $resolver->setAllowedTypes('additional_parameters', ['array']);

            $resolver->setAllowedTypes('redirect_route', ['string', 'null']);
            $resolver->setAllowedTypes('redirect_parameters_field_mapping', ['array']);
            $resolver->setAllowedTypes('redirect_additional_parameters', ['array']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'action';
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
