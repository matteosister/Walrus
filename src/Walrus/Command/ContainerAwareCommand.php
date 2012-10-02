<?php
/**
 * User: matteo
 * Date: 01/10/12
 * Time: 23.39
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\ArrayInput;

abstract class ContainerAwareCommand extends Command
{
    protected $container;

    /**
     * class constructor
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    final public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function runCommand($command, OutputInterface $output, $arguments = array())
    {
        $command = $this->getApplication()->find($command);
        $input = new ArrayInput(array_merge(array('command' => $command), $arguments));
        $command->run($input, $output);
    }

    /**
     * service getter
     *
     * @param string $name service id
     *
     * @return object
     */
    protected function get($name)
    {
        return $this->container->get($name);
    }

    /**
     * @return \Walrus\Utilities\Utilities
     */
    protected function getUtilities()
    {
        return $this->get('utilities');
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->get('twig');
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwigTheme()
    {
        return $this->get('twig.theme');
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwigMdContent()
    {
        return $this->get('twig.md_content');
    }

    /**
     * @return \Walrus\DI\Configuration
     */
    protected function getConfiguration()
    {
        return $this->get('walrus.configuration');
    }

    /**
     * @return \Walrus\Asset\AssetCollection
     */
    protected function getAssetCollection()
    {
        return $this->get('asset.projects_collection');
    }

    /**
     * @return \Walrus\Collection\PageCollection
     */
    protected function getPageCollection()
    {
        return $this->get('walrus.collection.page');
    }
}
