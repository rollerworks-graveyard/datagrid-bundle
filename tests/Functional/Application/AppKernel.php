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

namespace Rollerworks\Bundle\DatagridBundle\Tests\Functional\Application;

use Matthias\SymfonyServiceDefinitionValidator\Compiler\ValidateServiceDefinitionsPass;
use Matthias\SymfonyServiceDefinitionValidator\Configuration;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private $config;

    public function __construct($config, $debug = true)
    {
        parent::__construct('test', $debug);

        if (!(new Filesystem())->isAbsolutePath($config)) {
            $config = __DIR__.'/config/'.$config;
        }

        if (!file_exists($config)) {
            throw new \RuntimeException(sprintf('The config file "%s" does not exist.', $config));
        }

        $this->config = $config;
    }

    public function getName()
    {
        return 'RDatagrid';
    }

    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),

            new \Rollerworks\Bundle\DatagridBundle\RollerworksDatagridBundle(),
            new AppBundle\AppBundle(),
        ];

        return $bundles;
    }

    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $this->rootDir = str_replace('\\', '/', __DIR__);
        }

        return $this->rootDir;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->config);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/RDatagrid/'.substr(sha1($this->config), 0, 6);
    }

    public function serialize()
    {
        return serialize([$this->config, $this->isDebug()]);
    }

    public function unserialize($str)
    {
        call_user_func_array([$this, '__construct'], unserialize($str));
    }

    protected function prepareContainer(ContainerBuilder $container)
    {
        $extensions = [];

        foreach ($this->bundles as $bundle) {
            if ($extension = $bundle->getContainerExtension()) {
                $container->registerExtension($extension);
                $extensions[] = $extension->getAlias();
            }

            if ($this->debug) {
                $container->addObjectResource($bundle);
            }
        }

        foreach ($this->bundles as $bundle) {
            $bundle->build($container);
        }

        $this->buildBundleless($container);

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));
    }

    private function buildBundleless(ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.debug')) {
            $configuration = new Configuration();
            $configuration->setEvaluateExpressions(true);

            $container->addCompilerPass(
                new ValidateServiceDefinitionsPass($configuration),
                PassConfig::TYPE_AFTER_REMOVING
            );
        }
    }
}
