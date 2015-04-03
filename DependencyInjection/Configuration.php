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
        $rootNode = $treeBuilder->root('webridge_oauth2_access');

        $this->addServersSection($rootNode);

        return $treeBuilder;
    }

    private function addServersSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('upstream_base_url')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
    }
}
