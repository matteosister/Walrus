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

/**
 * startup:project command
 */
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
        $this->writeHeader($output);
        $fs = new Filesystem();
        $root = $this->container->getParameter('ROOT_PATH');
        $pages = $this->container->getParameter('DRAFTING_PATH').'/pages';
        $public = $this->container->getParameter('PUBLIC_PATH');
        foreach (array($pages, $public) as $folder) {
            if (!is_dir($folder)) {
                $output->writeln($this->getLine('creating', $folder.' folder'));
                $fs->mkdir($folder);
            }
        }
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
        }
        if (file_exists($root.'/vendor/bin/walrus') && !is_file($root.'/w')) {
            $output->writeln($this->getLine('symlink', 'walrus executable to root project as "w"'));
            $fs->symlink($root.'/vendor/bin/walrus', $root.'/w');
        }
        $output->writeln('<comment>Project ready!</comment>');
    }

    private function getDefaultConfiguration()
    {
        return array(
            'walrus' => array(
                'site_name' => 'My first Walrus website',
                'theme_name' => 'cypress',
                'theme_location' => null,
                'uglify_css' => array(
                    'enabled' => false,
                    'path' => '/usr/bin/uglifycss'
                ),
                'uglify_js' => array(
                    'enabled' => false,
                    'path' => '/usr/bin/uglifyjs'
                ),
            )
        );
    }
}
