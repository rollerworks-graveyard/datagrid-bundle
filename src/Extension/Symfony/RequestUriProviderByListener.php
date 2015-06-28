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

use Symfony\Component\HttpFoundation\Request;

class RequestUriProviderByListener implements RequestUriProviderInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @return string
     */
    public function getRequestUri()
    {
        if (null === $this->request) {
            throw new \LogicException(
                'No Request set for RequestUriProviderByListener. Do not use this service manually.'
            );
        }

        return $this->request->getRequestUri();
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }
}
