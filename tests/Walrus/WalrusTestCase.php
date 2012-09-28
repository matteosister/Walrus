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
use Walrus\Command\CreatePageCommand,
    Walrus\Command\CreatePostCommand,
    Walrus\Command\GenerateSiteCommand,
    Walrus\Collection\Collection;

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

    protected function getMockConfiguration()
    {
        $configuration = $this->getMock('Walrus\DI\Configuration', array('get'));
        $configuration->expects($this->any())
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
        ), array(Collection::TYPE_PAGES));
        $pageCollection->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(array()));
        return $pageCollection;
    }

    protected function getMockUtilities()
    {
        $utilities = $this->getMock('Walrus\Utilities\Utilities', array('slugify', 'getDateFormatted'));
        $utilities->expects($this->once())
            ->method('getDateFormatted')
            ->will($this->returnValue('2012-01-01_10:00:00'));
        $utilities->expects($this->once())
            ->method('slugify')
            ->will($this->returnValue('test'));

        return $utilities;
    }

    protected function getApplication()
    {
        $kernel = $this->getMock('Kernel');
        $application = new Application($kernel);
        $configuration = $this->getMockConfiguration();
        $twig = $this->getTwig();
        $utilities = $this->getMockUtilities();
        $application->add(new CreatePageCommand($configuration, $twig, $utilities, $this->getMockPageCollection()));

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
