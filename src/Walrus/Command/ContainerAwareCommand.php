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

/**
 * base class for command that needs container access
 */
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

    /**
     * run a command
     *
     * @param string                                            $command   command to run
     * @param \Symfony\Component\Console\Output\OutputInterface $output    output
     * @param array                                             $arguments args
     */
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
     * service parameter getter
     *
     * @param string $name    parameter name
     * @param null   $default default
     *
     * @return mixed
     */
    protected function getParameter($name, $default = null)
    {
        return $this->container->getParameter($name, $default);
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
     * @return \Walrus\Asset\AssetCollection
     */
    protected function getAssetCollection()
    {
        return $this->getProject()->getTheme()->getAssetCollection();
    }

    /**
     * @return \Walrus\Theme\Theme
     */
    protected function getTheme()
    {
        return $this->getProject()->getTheme();
    }

    /**
     * @return \Walrus\Project\Project
     */
    protected function getProject()
    {
        return $this->get('walrus.project');
    }

    /**
     * @return \Walrus\Collection\PageCollection
     */
    protected function getPageCollection()
    {
        return $this->get('walrus.collection.page');
    }
}
