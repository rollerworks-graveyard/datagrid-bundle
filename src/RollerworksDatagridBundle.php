<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\DatagridBundle;

use Rollerworks\Component\DatagridBundle\DependencyInjection\Compiler\ExtensionPass;
use Rollerworks\Component\DatagridBundle\DependencyInjection\DatagridExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class RollerworksDatagridBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ExtensionPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new DatagridExtension();
        }

        if ($this->extension) {
            return $this->extension;
        }
    }

    public function registerCommands(Application $application)
    {
        // noop
    }
}
