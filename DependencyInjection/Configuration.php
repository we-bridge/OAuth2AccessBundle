<?php

namespace Webridge\Oauth2AccessBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('oauth2access');

        $this->addServersSection($rootNode);

        return $treeBuilder;
    }

    private function addServersSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->booleanNode('upstream_base_url:')->isRequired()->end()
            ->end()
        ;
    }
}
