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
                    //->useAttributeAsKey('type')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('type')
                            ->validate()
                                ->ifNotInArray(array('compass', 'less', 'css_source', 'js_source'))
                                ->thenInvalid('Invalid asset project name %s')
                            ->end()
                        ->end()
                        ->scalarNode('source_file')->defaultNull()->end()
                        ->scalarNode('source_folder')->defaultNull()->end()
                        ->scalarNode('destination_file')->defaultNull()->end()
                        ->scalarNode('compress')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
