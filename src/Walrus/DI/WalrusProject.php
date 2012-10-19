<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.11
 *
 * Just for fun...
 */

namespace Walrus\DI;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\YamlFileLoader,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\Console\Application,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Yaml\Yaml;
use Walrus\Configuration\MainConfiguration;
use Walrus\Utilities\SlugifierTrait;

/**
 * base project class
 */
class WalrusProject
{
    use SlugifierTrait;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Console\Application
     */
    private $application;

    /**
     * class constructor
     *
     * @param string $rootPath root of the project
     */
    public function __construct($rootPath)
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter("ROOT_PATH", realpath($rootPath));
        $this->container->setParameter("WALRUS_PATH", realpath(__DIR__.'/../../../'));
        $this->container->setParameter("PUBLIC_PATH", $this->container->getParameter('ROOT_PATH').'/public');
        $this->container->setParameter("DRAFTING_PATH", $this->container->getParameter('ROOT_PATH').'/drafting');
        $this->container->setParameter('THEME_PATH', $this->container->getParameter('WALRUS_PATH').'/themes/cypress');
        $this->loadWalrusDI();
        $this->container->compile();
    }

    /**
     * get application
     *
     * @return \Symfony\Component\Console\Application
     */
    public function getApplication()
    {
        if (null === $this->application) {
            $this->application = new Application();
            $this->application->add($this->container->get('startup_project.command'));
            $this->application->add($this->container->get('create_page.command'));
            $this->application->add($this->container->get('generate_site.command'));
            $this->application->add($this->container->get('startup_server.command'));
            $this->application->add($this->container->get('project_watch.command'));
        }

        return $this->application;
    }

    private function loadWalrusDI()
    {
        $loader = new YamlFileLoader($this->container, new FileLocator(array(__DIR__.'/../Resources/config')));
        $loader->load('templating.yml');
        $loader->load('commands.yml');
        $loader->load('utilities.yml');
        $loader->load('collections.yml');
        $loader->load('markdown.yml');
        $loader->load('theme.yml');
        $loader->load('project.yml');

        // TODO: load different filters for css and js, maybe a compiler pass?
        /*$project = $this->container->get('walrus.project');
        if (null !== $cssCompressor = $project->getCssCompressor()) {
            $this->container->setParameter('asset.css.compressor.path', $cssCompressor['path']);
        }
        if (null !== $jsCompressor = $project->getJsCompressor()) {
            $this->container->setParameter('asset.js.compressor.path', $jsCompressor['path']);
        }
        if ($cssCompressor && $jsCompressor) {
            $loader->load(sprintf('assets/assets_%s.yml', $cssCompressor['name']));
        } else {
            $loader->load('assets/assets.yml');
        }*/

        $loader->load('assets/assets.yml');
    }

    /**
     * Container getter
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
