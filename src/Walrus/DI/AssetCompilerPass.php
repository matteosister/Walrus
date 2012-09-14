<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 15.10
 *
 * Just for fun...
 */

namespace Walrus\DI;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\Reference;

class AssetCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('asset.projects_collection')) {
            return;
        }

        $definition = $container->getDefinition('asset.projects_collection');

        foreach ($container->findTaggedServiceIds('asset.project') as $id => $attributes) {
            $definition->addMethodCall('addProject', array(new Reference($id)));
        }
    }
}
