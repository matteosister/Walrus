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
    Symfony\Component\Config\FileLocator,
    Symfony\Component\Console\Application,
    Symfony\Component\Yaml\Parser,
    Symfony\Component\Finder\Finder;

use Walrus\DI\AssetCompilerPass;

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
        $container = new ContainerBuilder();
        $container->addCompilerPass(new AssetCompilerPass());
        $container->setParameter("ROOT_PATH", realpath($rootPath));
        $container->setParameter("PUBLIC_PATH", $container->getParameter('ROOT_PATH').'/public');

        $parser = new Parser();
        $values = $parser->parse(file_get_contents($container->getParameter('ROOT_PATH').'/config.yml'));

        $container->setParameter('THEME_PATH', $container->getParameter('ROOT_PATH').'/themes/'.$values['walrus']['theme']);
        
        $loader = new YamlFileLoader($container, new FileLocator(array(
            __DIR__.'/Resources/config',
            $container->getParameter('THEME_PATH').'/di'
        )));
        $loader->load('templating.yml');
        $loader->load('commands.yml');
        $loader->load('utilities.yml');
        $loader->load('configuration.yml');
        $loader->load('assets.yml');

        $finder = new Finder();
        $iterator = $finder->files()->in($container->getParameter('THEME_PATH').'/di')->name('*.yml');
        foreach($iterator as $file) {
            $loader->load($file->getRelativePathname());
        }
        $container->compile();
        $this->container = $container;
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
}
