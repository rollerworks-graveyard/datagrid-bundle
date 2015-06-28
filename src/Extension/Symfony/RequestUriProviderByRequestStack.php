<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle\Extension\Symfony;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestUriProviderByRequestStack implements RequestUriProviderInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestStack->getMasterRequest()->getRequestUri();
    }
}
