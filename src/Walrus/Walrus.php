<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.11
 *
 * Just for fun...
 */

namespace Walrus;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\YamlFileLoader,
    Symfony\Component\DependencyInjection\Definition,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\Console\Application,
    Symfony\Component\Yaml\Parser,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Yaml\Yaml;

use Walrus\DI\AssetCompilerPass,
    Walrus\Configuration\ThemeConfiguration;

class Walrus
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Console\Application
     */
    private $application;

    public function __construct($rootPath)
    {
        $this->container = new ContainerBuilder();
        $this->container->addCompilerPass(new AssetCompilerPass());
        $this->container->setParameter("ROOT_PATH", realpath($rootPath));
        $this->container->setParameter("PUBLIC_PATH", $this->container->getParameter('ROOT_PATH').'/public');

        $parser = new Parser();
        $values = $parser->parse(file_get_contents($this->container->getParameter('ROOT_PATH').'/config.yml'));

        $this->container->setParameter('THEME_PATH', $this->container->getParameter('ROOT_PATH').'/themes/'.$values['walrus']['theme']);
        $this->loadDI();
        $this->loadThemeConfiguration();
        $this->container->compile();
    }

    public function getApplication()
    {
        if (null === $this->application) {
            $this->application = new Application();

            $this->application->add($this->container->get('create_post.command'));
            $this->application->add($this->container->get('create_page.command'));
            $this->application->add($this->container->get('generate_site.command'));
        }
        return $this->application;
    }

    private function loadThemeConfiguration()
    {
        $config = Yaml::parse($this->container->getParameter('THEME_PATH').'/theme.yml');
        $processor = new Processor();
        $conf = new ThemeConfiguration();
        $pc = $processor->processConfiguration($conf, $config);
        if (null !== $less = $pc['assets']['less']) {
            $sourceFile = $this->container->getParameter('THEME_PATH').'/'.$less['source_file'];
            if (is_file($sourceFile)) {
                $pathParts = pathinfo($sourceFile);
                $dir = $pathParts['dirname'];
                $name = $pathParts['basename'];
                $lessProject = new \LessElephant\LessProject($dir, $name, $this->container->getParameter('PUBLIC_PATH').'/css/bootstrap.css');
                $def = new Definition('Walrus\Asset\Project\Less', array($lessProject));
                $def->addTag('asset.project');
                $this->container->addDefinitions(array('walrus.asset.less.project' => $def));
            } else {
                throw new \RuntimeException(sprintf('the file %s do not exists', $sourceFile));
            }
        }
    }

    private function loadDI()
    {
        $loader = new YamlFileLoader($this->container, new FileLocator(array(
            __DIR__.'/Resources/config',
            $this->container->getParameter('THEME_PATH').'/di'
        )));
        $loader->load('templating.yml');
        $loader->load('commands.yml');
        $loader->load('utilities.yml');
        $loader->load('configuration.yml');
        $loader->load('assets.yml');

        $finder = new Finder();
        $iterator = $finder->files()->in($this->container->getParameter('THEME_PATH').'/di')->name('*.yml');
        foreach($iterator as $file) {
            $loader->load($file->getRelativePathname());
        }
    }
}
