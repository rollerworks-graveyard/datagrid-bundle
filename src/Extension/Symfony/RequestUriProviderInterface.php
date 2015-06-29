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

/**
 * The RequestUriProvide provides access to the current URI of the master-request.
 *
 * This interface only exists to be compatible with Symfony <2.4,
 * where the RequestStack did not exist yet.
 */
interface RequestUriProviderInterface
{
    /**
     * @return string
     */
    public function getRequestUri();
}
