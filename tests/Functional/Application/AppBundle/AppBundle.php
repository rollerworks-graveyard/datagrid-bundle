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

namespace Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle;

use Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application\AppBundle\DependencyInjection\AppExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new AppExtension();
        }

        if ($this->extension) {
            return $this->extension;
        }
    }
}
