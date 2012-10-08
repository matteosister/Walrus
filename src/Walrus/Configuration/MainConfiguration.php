<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 22.37
 *
 * Just for fun...
 */

namespace Walrus\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface,
    Symfony\Component\Config\Definition\Builder\TreeBuilder;

class MainConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('walrus');
        $rootNode
            ->children()
                ->scalarNode('site_name')->defaultValue('My first Walrus website')->end()
                ->scalarNode('theme')->defaultValue('cypress')->end()
                ->scalarNode('theme_location')->defaultNull()->end()
            ->end();
        return $treeBuilder;
    }
}
