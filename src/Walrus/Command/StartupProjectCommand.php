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
    Symfony\Component\Filesystem\Filesystem,
    Symfony\Component\Yaml\Yaml,
    Symfony\Component\Config\Definition\Processor;
use Walrus\Command\OutputWriterTrait,
    Walrus\Command\ContainerAwareCommand,
    Walrus\Configuration\MainConfiguration;


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
        $output->writeln($this->getLine('creating', 'base folders'));
        $fs->mkdir(array($pages, $public));
        $file = $root.'/walrus.yml';
        if (!file_exists($file)) {
            $output->writeln($this->getLine('creating', 'base config file <comment>walrus.yml</comment>'));
            file_put_contents($file, Yaml::dump($this->getDefaultConfiguration()));
        } else {
            $output->writeln($this->getLine('checking', '<comment>config.yml</comment> file'));
            $config = Yaml::parse($root.'/walrus.yml');
            $processor = new Processor();
            $conf = new MainConfiguration();
            $processor->processConfiguration($conf, $config);
            $output->writeln('<comment>DONE!</comment>');
        }
    }

    private function getDefaultConfiguration()
    {
        return array(
            'walrus' => array(
                'site_name' => 'My first Walrus website',
                'theme' => 'cypress',
                'theme_location' => null
            )
        );
    }
}
