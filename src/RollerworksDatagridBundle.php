<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\DatagridBundle;

use Rollerworks\Bundle\DatagridBundle\DependencyInjection\Compiler as CompilerPass;
use Rollerworks\Bundle\DatagridBundle\DependencyInjection\DatagridExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class RollerworksDatagridBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompilerPass\TwigRenderEnginePass());
        $container->addCompilerPass(new CompilerPass\ExtensionPass());
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
}
