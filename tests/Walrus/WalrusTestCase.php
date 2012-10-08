<?php
/**
 * User: matteo
 * Date: 09/09/12
 * Time: 23.25
 *
 * Just for fun...
 */

namespace Walrus;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Walrus\Command\CreatePageCommand;

/**
 * base test case
 */
class WalrusTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    protected $filesystem;
    protected $playgroundDir;
    protected $draftingDir;
    protected $pagesDir;
    protected $postsDir;
    protected $assetsProjectsDir;
    protected $assetsProjectsPublishDir;
    protected $cssSourceProjectDir;

    const DATE_FORMAT = 'Y-m-d_H:i:s';
    const PAGE_TITLE = 'The Test Title';
    const PAGE_DATE = '2012-01-01_10:00:00';
    const PAGE_URL = 'the-test-title';
    const PAGE_HOMEPAGE = false;
    const PAGE_CONTENT = 'default test **content**';
    const PAGE_TYPE = 'default';

    protected function setUp()
    {
        $this->filesystem = new Filesystem();
        $this->playgroundDir = __DIR__.'/Playground';
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
    }

    protected function tearDown()
    {
        $finder = new Finder();
        $iterator = $finder->files()->in(array(
            $this->pagesDir,
            $this->postsDir,
            $this->assetsProjectsDir
        ));
        $this->filesystem->remove($iterator);
        $this->filesystem->remove($this->pagesDir);
        $this->filesystem->remove($this->postsDir);
        $this->filesystem->remove($this->draftingDir);
        $this->filesystem->remove($this->assetsProjectsDir);
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
            $content = $this->getMDPageContent('test '.$i, null, 'test-'.$i);
            $filename = $this->draftingDir.'/pages/'.sprintf('12-01-01_10:00:00_page_test-%s.md', $i);
            file_put_contents($filename, $content);
        }
    }

    protected function getTwig()
    {
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../src/Walrus/Resources/tpl');
        return new \Twig_Environment($loader, array('cache' => false));
    }

    protected function getMockContainer()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\Container', array('get'));
        $container
            ->expects($this->any())
            ->method('get')
            ->with($this->logicalOr(
                $this->equalTo('utilities'),
                $this->equalTo('walrus.configuration'),
                $this->equalTo('walrus.collection.page'),
                $this->equalTo('twig')
            ))
            ->will($this->returnCallback(array($this, 'containerGetCallback')));

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
        $assetCollection = $this->getMock('Walrus\Asset\AssetCollection', array());
        return $assetCollection;
    }

    protected function getMockPageCollection()
    {
        $pageCollection = $this->getMock('Walrus\Collection\PageCollection', array(
            'toArray'
        ), array());
        $pageCollection->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(array()));
        return $pageCollection;
    }



    protected function getMockUtilities()
    {
        $utilities = $this->getMock('Walrus\Utilities\Utilities', array('slugify', 'getDateFormatted'));
        $utilities->expects($this->any())
            ->method('getDateFormatted')
            ->will($this->returnValue('2012-01-01_10:00:00'));
        $utilities->expects($this->any())
            ->method('slugify')
            ->will($this->returnValue('test'));

        return $utilities;
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
        $configuration = $this->getMockConfiguration();
        $twig = $this->getTwig();
        $utilities = $this->getMockUtilities();
        $application->add(new CreatePageCommand($this->getMockContainer()));

        return $application;
    }

    protected function getMDPageContent(
        $title = null, $date = null, $url = null, $homepage = false, $content = null, $type = null)
    {
        $title = $this->defaultValue($title, static::PAGE_TITLE);
        $date = $this->defaultValue($date, static::PAGE_DATE);
        $url = $this->defaultValue($url, static::PAGE_URL);
        $homepage = $this->defaultValue($homepage, static::PAGE_HOMEPAGE);
        $content = $this->defaultValue($content, static::PAGE_CONTENT);
        $type = $this->defaultValue($type, static::PAGE_TYPE);
        return sprintf('***
title: %s
date: %s
url: %s
parent:
homepage: %s
type: %s
***
%s', $title, $date, $url, $homepage, $type, $content);
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
}
