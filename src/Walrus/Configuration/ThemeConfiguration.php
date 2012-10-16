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
 * theme configuration definition
 */
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
                ->scalarNode('images')->defaultNull()->end()
                ->scalarNode('compress_assets')->defaultFalse()->end()
                ->scalarNode('group_assets')->defaultFalse()->end()
                ->arrayNode('assets')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('type')
                            ->validate()
                                ->ifNotInArray(array('compass', 'less', 'css_source', 'js_source', 'js_file'))
                                ->thenInvalid('Invalid asset project name %s')
                            ->end()
                        ->end()
                        ->scalarNode('source_file')->defaultNull()->end()
                        ->scalarNode('source_folder')->defaultNull()->end()
                        ->scalarNode('destination_file')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
