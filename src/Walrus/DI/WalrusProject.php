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
    Symfony\Component\DependencyInjection\Definition,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\Console\Application,
    Symfony\Component\Yaml\Parser,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Yaml\Yaml;
use Walrus\DI\AssetCompilerPass,
    Walrus\Configuration\ThemeConfiguration;
use LessElephant\LessProject;
use CompassElephant\CompassProject;
use Walrus\Utilities\SlugifierTrait;

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
        $this->container->addCompilerPass(new AssetCompilerPass());
        $this->container->setParameter("ROOT_PATH", realpath($rootPath));
        $this->container->setParameter("WALRUS_PATH", realpath(__DIR__.'/../../../'));
        $this->container->setParameter("PUBLIC_PATH", $this->container->getParameter('ROOT_PATH').'/public');
        $this->container->setParameter('THEME_PATH', __DIR__.'/../../../themes/cypress');
        $this->loadWalrusDI();
        $this->loadThemeConfiguration();
        $this->container->compile();
    }

    public function getApplication()
    {
        if (null === $this->application) {
            $this->application = new Application();
            $this->application->add($this->container->get('startup_project.command'));
            $this->application->add($this->container->get('create_page.command'));
            $this->application->add($this->container->get('generate_site.command'));
            $this->application->add($this->container->get('startup_server.command'));
        }
        return $this->application;
    }

    private function loadWalrusDI()
    {
        $loader = new YamlFileLoader($this->container, new FileLocator(array(__DIR__.'/../Resources/config')));
        $loader->load('templating.yml');
        $loader->load('commands.yml');
        $loader->load('utilities.yml');
        $loader->load('configuration.yml');
        $loader->load('assets.yml');
        $loader->load('collections.yml');
        $loader->load('markdown.yml');
    }

    private function loadThemeConfiguration()
    {
        $config = Yaml::parse($this->container->getParameter('THEME_PATH').'/theme.yml');
        $processor = new Processor();
        $conf = new ThemeConfiguration();
        $pc = $processor->processConfiguration($conf, $config);
        foreach($pc['assets'] as $assetsConfiguration) {
            switch($assetsConfiguration['type']) {
                case 'compass':
                    $this->compassConfiguration($assetsConfiguration);
                    break;
                case 'less':
                    $this->lessConfiguration($assetsConfiguration);
                    break;
                case 'css_source':
                    $this->cssFolderConfiguration($assetsConfiguration);
                    break;
                case 'js_source':
                    $this->jsFolderConfiguration($assetsConfiguration);
                    break;
            }
        }
    }

    private function compassConfiguration($conf)
    {
        $sourceFolder = $this->container->getParameter('THEME_PATH').'/'.$conf['source_folder'];
        if (is_dir($sourceFolder)) {
            $compassProject = new CompassProject($sourceFolder);
            $def = new Definition('Walrus\Asset\Project\Css\Compass', array($compassProject, $conf['name']));
            if ($conf['compress']) {
                $def->addMethodCall('setCompress', array($conf['compress']));
            }
            $def->addTag('asset.project');
            $this->container->addDefinitions(array(sprintf('walrus.asset.compass.project.%s', $this->slugify($conf['name'])) => $def));
        } else {
            throw new \RuntimeException(sprintf('the folder %s do not exists, the compass project couldn\'t be initalized', $sourceFolder));
        }
    }

    private function lessConfiguration($conf)
    {
        $sourceFile = $this->container->getParameter('THEME_PATH').'/'.$conf['source_file'];
        $destFile = $this->container->getParameter('THEME_PATH').'/'.$conf['destination_file'];
        if (is_file($sourceFile)) {
            $pathParts = pathinfo($sourceFile);
            $dir = $pathParts['dirname'];
            $name = $pathParts['basename'];
            $lessProject = new LessProject($dir, $name, $destFile);
            $def = new Definition('Walrus\Asset\Project\Css\Less', array($lessProject, $conf['name']));
            if ($conf['compress']) {
                $def->addMethodCall('setCompress', array($conf['compress']));
            }
            $def->addTag('asset.project');
            $this->container->addDefinitions(array(sprintf('walrus.asset.less.project.%s', $this->slugify($name)) => $def));
        } else {
            throw new \RuntimeException(sprintf('the file %s do not exists, the less project could not be initialized', $sourceFile));
        }
    }

    private function cssFolderConfiguration($conf)
    {
        // TODO: validate folder paths
        $fileFolder = $this->container->getParameter('THEME_PATH').'/'.$conf['source_folder'];
        $def = new Definition('Walrus\Asset\Project\Css\CssFolder', array($fileFolder, $conf['name']));
        if ($conf['compress']) {
            $def->addMethodCall('setCompress', array($conf['compress']));
        }
        $def->addTag('asset.project');
        $this->container->addDefinitions(array(sprintf('walrus.asset.css_folder.project.%s', $conf['name']) => $def));
    }

    private function jsFolderConfiguration($conf)
    {
        // TODO: validate folder paths
        $fileFolder = $this->container->getParameter('THEME_PATH').'/'.$conf['source_folder'];
        $def = new Definition('Walrus\Asset\Project\Js\JsFolder', array($fileFolder, $conf['name']));
        if ($conf['compress']) {
            $def->addMethodCall('setCompress', array($conf['compress']));
        }
        $def->addTag('asset.project');
        $this->container->addDefinitions(array(sprintf('walrus.asset.js_folder.project.%s', $conf['name']) => $def));
    }
}
