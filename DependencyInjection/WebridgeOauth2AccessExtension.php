<?php

namespace Webridge\Oauth2AccessBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WebridgeOauth2AccessExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
        $configuration = new Configuration();
        $extensionConfig = $this->processConfiguration(
            $configuration,
            $configs
        );

        $container->setParameter(
            'oauth2access.upstream_base_url',
            $extensionConfig['default_transport']
        );
    }

    public function getAlias()
    {
        return 'webridge_oauth2access';
    }
}
