<?php
/**
 * User: matteo
 * Date: 09/09/12
 * Time: 23.25
 *
 * Just for fun...
 */

namespace Walrus;

use Symfony\Component\Finder\Finder,
    Symfony\Component\Console\Application,
    Symfony\Component\Filesystem\Filesystem,
    Symfony\Component\Yaml\Yaml;
use Walrus\Command\CreatePageCommand,
    Walrus\Command\GenerateSiteCommand,
    Walrus\Utilities\SlugifierTrait,
    Walrus\MDObject\Page\Page,
    Walrus\Twig\Extension\MdContentExtension,
    Dflydev\Twig\Extension\GitHubGist\GistTwigExtension,
    Walrus\Twig\Extension\ThemeExtension;

/**
 * base test case
 */
class WalrusTestCase extends \PHPUnit_Framework_TestCase
{
    use SlugifierTrait;

    /**
     * @var Filesystem
     */
    protected $filesystem;
    protected $playgroundDir;
    protected $fixturesDir;
    protected $draftingDir;
    protected $pagesDir;
    protected $postsDir;
    protected $assetsProjectsDir;
    protected $assetsProjectsPublishDir;
    protected $cssSourceProjectDir;
    protected $generatedPages;

    const DATE_FORMAT = 'Y-m-d_H:i:s';
    const DATE_DEFAULT = '2012-01-01_10:00:00';
    const PAGE_TITLE = 'The Test Title';
    const PAGE_DATE = '2012-01-01_10:00:00';
    const PAGE_URL = 'the-test-title';
    const PAGE_HOMEPAGE = false;
    const PAGE_CONTENT = 'default test **content**';
    const PAGE_TYPE = 'default';

    protected function setUp()
    {
        $this->filesystem = new Filesystem();
        $this->playgroundDir = realpath(__DIR__.'/../playground');
        $this->fixturesDir = realpath(__DIR__.'/../fixtures');
        $this->draftingDir = $this->playgroundDir.'/drafting';
        $this->assetsProjectsDir= $this->playgroundDir.'/assets_projects';
        $this->createFolderIfNotExists($this->draftingDir);
        $this->pagesDir = $this->draftingDir.'/pages';
        $this->createFolderIfNotExists($this->pagesDir);
        $this->postsDir = $this->draftingDir.'/posts';
        $this->createFolderIfNotExists($this->postsDir);
        $this->createFolderIfNotExists($this->assetsProjectsDir);
        $this->assetsProjectsPublishDir = $this->assetsProjectsDir . '/compile';
        $this->createFolderIfNotExists($this->assetsProjectsPublishDir);
        $this->cssSourceProjectDir = $this->assetsProjectsDir.'/css_source';
        $this->createFolderIfNotExists($this->cssSourceProjectDir);
        $this->generatedPages = array();
    }

    protected function tearDown()
    {
        $finder = new Finder();
        $iterator = $finder->in($this->playgroundDir);
        $this->filesystem->remove($iterator);
        $this->generatedPages = array();
    }

    private function createFolderIfNotExists($folder)
    {
        if (!$this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
    }

    protected function addRandomPages($num = 1)
    {
        for ($i = 1; $i <= $num; $i++) {
            if (0 === count($this->generatedPages)) {
                $content = $this->getMDPageContent('homepage', null, 'homepage', true, null);
            } else {
                $content = $this->getMDPageContent('test '.$i, null, 'test-'.$i, false, $i == 1 ? null : 'homepage');
            }
            $number = str_pad($i, 4, '0', STR_PAD_RIGHT);
            $filename = $this->pagesDir.'/'.sprintf('%s-test-%s.md', $number, $i);
            file_put_contents($filename, $content);
            $this->generatedPages[] = new Page($filename);
        }
    }

    protected function getTwig()
    {
        //\Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../src/Walrus/Resources/tpl');
        return new \Twig_Environment($loader, array('cache' => false));
    }

    protected function getTwigMdContent()
    {
        //\Twig_Autoloader::register();
        $loader = new \Twig_Loader_String();
        $env = new \Twig_Environment($loader, array('cache' => false));
        $env->addExtension(new MdContentExtension($this->getMockContainer()));
        $env->addExtension(new GistTwigExtension());
        return $env;
    }

    protected function getTwigTheme($name = 'test1')
    {
        //\Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem($this->fixturesDir.sprintf('/themes/%s/templates', $name));
        $env = new \Twig_Environment($loader, array('cache' => false));
        $env->addExtension(new ThemeExtension($this->getMockContainer()));
        return $env;
    }

    protected function getMockContainer()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get', 'getParameter'));
        $container
            ->expects($this->any())
            ->method('get')
            ->with($this->logicalOr(
                $this->equalTo('utilities'),
                $this->equalTo('walrus.configuration'),
                $this->equalTo('walrus.collection.page'),
                $this->equalTo('twig'),
                $this->equalTo('twig.md_content'),
                $this->equalTo('twig.theme'),
                $this->equalTo('walrus.theme'),
                $this->equalTo('walrus.project')
            ))
            ->will($this->returnCallback(array($this, 'containerGetCallback')));
        $container
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->logicalOr(
                $this->equalTo('DRAFTING_PATH'),
                $this->equalTo('ROOT_PATH'),
                $this->equalTo('PUBLIC_PATH')
            ))
            ->will($this->returnCallback(array($this, 'containerGetParameterCallback')));

        return $container;
    }

    public function containerGetCallback($service)
    {
        switch ($service) {
            case 'walrus.configuration':
                return $this->getMockConfiguration();
                break;
            case 'utilities':
                return $this->getMockUtilities();
                break;
            case 'walrus.collection.page':
                return $this->getMockPageCollection();
                break;
            case 'twig':
                return $this->getTwig();
                break;
            case 'walrus.theme':
                return $this->getMockTheme();
                break;
            case 'twig.md_content':
                return $this->getTwigMdContent();
                break;
            case 'twig.theme':
                return $this->getTwigTheme();
                break;
            case 'walrus.project':
                return $this->getMockProject();
                break;
        }
    }

    public function containerGetParameterCallback($name)
    {
        switch ($name) {
            case 'DRAFTING_PATH':
                return $this->draftingDir;
                break;
            case 'ROOT_PATH':
                return $this->playgroundDir;
                break;
            case 'PUBLIC_PATH':
                return $this->playgroundDir.'/public';
                break;
        }
    }

    protected function getMockConfiguration()
    {
        $configuration = $this->getMock('Walrus\DI\Configuration', array('get'));
        $configuration
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('drafting_dir'))
            ->will($this->returnValue(realpath(__DIR__.'/Playground/drafting')));

        return $configuration;
    }

    protected function getMockAssetCollection()
    {
        $assetCollection = $this->getMock('Walrus\Asset\AssetCollection', array('output'));
        $assetCollection->expects($this->any())
            ->method('output')
            ->will($this->returnValue(''));
        return $assetCollection;
    }

    protected function getMockPageCollection()
    {
        $mock = $this->getMock('Walrus\Collection\PageCollection');
        $mock->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($this->generatedPages));
        $mock->expects($this->any())
            ->method('count')
            ->will($this->returnValue(count($this->generatedPages)));
        //$this->expectIterator($mock, $this->generatedPages);

        return $mock;
    }

    public function expectIterator($mock, array $content, $withKey = false, $counter = 0)
    {
        $mock
            ->expects($this->at($counter))
            ->method('rewind');

        foreach ($content as $key => $value) {
            $mock
                ->expects($this->at(++$counter))
                ->method('valid')
                ->will($this->returnValue(true));

            $mock
                ->expects($this->at(++$counter))
                ->method('current')
                ->will($this->returnValue($value));

            if ($withKey) {
                $mock
                    ->expects($this->at(++$counter))
                    ->method('key')
                    ->will($this->returnValue($key));
            }

            $mock
                ->expects($this->at(++$counter))
                ->method('next');
        }

        $mock
            ->expects($this->at(++$counter))
            ->method('valid')
            ->will($this->returnValue(false));

        return ++$counter;
    }


    protected function getMockTheme($name = 'test1')
    {
        $theme = $this->getMock('Walrus\Theme\Theme', array('getAssetCollection'), array($this->fixturesDir.sprintf('/themes/%s', $name), $this->getMockAssetCollection()));
        $theme->expects($this->any())
            ->method('getAssetCollection')
            ->will($this->returnValue($this->getMockAssetCollection()));
        return $theme;
    }

    protected function getMockUtilities()
    {
        $utilities = $this->getMock('Walrus\Utilities\Utilities', array('slugify', 'getDateFormatted', 'getUniqueSlug'));
        $utilities->expects($this->any())
            ->method('getDateFormatted')
            ->will($this->returnValue(static::DATE_DEFAULT));
        $utilities->expects($this->any())
            ->method('slugify')
            ->will($this->returnValue('test'));
        $utilities->expects($this->any())
            ->method('getUniqueSlug')
            ->will($this->returnCallback(array($this, 'callbackUniqueUrl')));

        return $utilities;
    }

    protected function getMockProject()
    {
        $project = $this->getMock('Walrus\Project\Project', array(), array($this->playgroundDir));
        return $project;
    }

    public function callbackUniqueUrl()
    {
        $args = func_get_args();
        return $this->slugify($args[1]);
    }

    protected function getMockCssFolder()
    {
        $cssFolder = $this->getMock('Walrus\Asset\Project\Css\CssFolder', array('getProjectType'), array(''));
        $cssFolder->expects($this->any())
            ->method('getProjectType')
            ->will($this->returnValue(\Walrus\Asset\Project\Css\CssFolder::TYPE_CSS));
        return $cssFolder;
    }

    protected function getMockJsFolder()
    {
        $jsFolder = $this->getMock('Walrus\Asset\Project\Js\JsFolder', array('getProjectType'), array(''));
        $jsFolder->expects($this->any())
            ->method('getProjectType')
            ->will($this->returnValue(\Walrus\Asset\Project\Css\CssFolder::TYPE_JS));
        return $jsFolder;
    }

    protected function getApplication()
    {
        $kernel = $this->getMock('Kernel');
        $application = new Application($kernel);
        $application->add(new CreatePageCommand($this->getMockContainer()));
        $application->add(new GenerateSiteCommand($this->getMockContainer()));

        return $application;
    }

    protected function getFixtureFile($name)
    {
        return $this->fixturesDir.'/'.$name;
    }

    protected function getMDPageContent($title = null, $date = null, $url = null, $homepage = false, $parent = null)
    {
        $options = compact('title', 'date', 'url', 'parent', 'homepage');
        $output = $this->getTwig()->render('page.md.twig', array(
            'options' => Yaml::dump($options),
            'title' => $title
        ));
        return $output;
    }

    protected function defaultValue($var, $defaultValue, $check = array(null, false))
    {
        return in_array($var, $check) ? $defaultValue : $var;
    }

    protected function iteratorTest($iterator)
    {
        $object = 'test';
        $this->assertCount(0, $iterator);
        $iterator[] = $object;
        $this->assertCount(1, $iterator);
        foreach($iterator as $i => $sub) {
            $this->assertNotNull($sub);
            $this->assertNotNull($i);
        }
        $iterator[0] = null;
        $this->assertEquals(null, $iterator[0]);
        unset($iterator[0]);
        $iterator[0] = $object;
        $this->assertCount(1, $iterator);
        $this->assertTrue(isset($iterator[0]));
    }

    protected function pageFileExists($slug)
    {
        $this->assertFileExists($this->pagesDir.'/'.sprintf('%s.md', $slug), sprintf('The page file %s has not been created', $slug));
    }
}
