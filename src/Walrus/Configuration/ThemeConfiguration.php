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

class ThemeConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('theme');
        $rootNode
            ->children()
                ->scalarNode('name')->isRequired()->end()
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('less')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('source_file')->isRequired()->end()
                            ->end()
                        ->end()
                        ->arrayNode('compass')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('source_folder')->isRequired()->end()
                            ->end()
                        ->end()
                        ->arrayNode('css_source')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
