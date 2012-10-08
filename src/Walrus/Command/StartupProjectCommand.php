<?php
/**
 * User: matteo
 * Date: 28/09/12
 * Time: 17.42
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Filesystem\Filesystem;
use Walrus\Command\OutputWriterTrait,
    Walrus\Command\ContainerAwareCommand;


class StartupProjectCommand extends ContainerAwareCommand
{
    use OutputWriterTrait;

    protected function configure()
    {
        return $this
            ->setName('startup:project')
            ->setDescription('Create the project folder structure');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $root = $this->container->getParameter('ROOT_PATH');
        $pages = $this->container->getParameter('DRAFTING_PATH').'/pages';
        $public = $this->container->getParameter('PUBLIC_PATH');
        $fs->mkdir(array($pages, $public));
    }
}
