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

/**
 * main configuration definition
 */
class MainConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $mainBuilder = new TreeBuilder();
        $rootNode = $mainBuilder->root('walrus');
        $rootNode
            ->children()
                ->scalarNode('site_name')->defaultValue('My first Walrus website')->end()
                ->scalarNode('theme_name')->defaultValue('cypress')->end()
                ->scalarNode('theme_location')->defaultNull()->end()
                ->arrayNode('uglify_css')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('path')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('uglify_js')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('path')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        return $mainBuilder;
    }
}
