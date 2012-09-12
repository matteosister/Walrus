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
use Walrus\Command\CreatePageCommand,
    Walrus\Command\CreatePostCommand,
    Walrus\Command\GenerateSiteCommand;

require_once __DIR__ . '/../../../vendor/twig/twig/lib/Twig/Autoloader.php';

/**
 * base test case
 */
class WalrusTestCase extends \PHPUnit_Framework_TestCase
{
    protected $playgroundDir;
    protected $draftingDir;
    protected $pagesDir;

    const DATE_FORMAT = 'Y-m-d_H:i:s';
    const PAGE_TITLE = 'The Test Title';
    const PAGE_DATE = '2012-01-01_10:00:00';
    const PAGE_URL = 'the-test-title';
    const PAGE_HOMEPAGE = false;
    const PAGE_CONTENT = 'default test **content**';
    const PAGE_TYPE = 'default';

    protected function setUp()
    {
        $this->playgroundDir = __DIR__.'/Playground';
        $this->draftingDir = $this->playgroundDir.'/drafting';
        if (!is_dir($this->draftingDir)) {
            mkdir($this->draftingDir);
        }
        $this->pagesDir = $this->draftingDir.'/pages';
        if (!is_dir($this->pagesDir)) {
            mkdir($this->pagesDir);
        }
    }

    protected function tearDown()
    {
        $finder = new Finder();
        $iterator = $finder->files()->in($this->draftingDir.'/pages');
        foreach($iterator as $file) {
            unlink($file);
        }
        rmdir($this->pagesDir);
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
        $configuration->expects($this->once())
            ->method('get')
            ->with($this->equalTo('drafing_dir'))
            ->will($this->returnValue(realpath(__DIR__.'/Playground/drafting')));

        return $configuration;
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
        $application->add(new CreatePageCommand($configuration, $twig, $utilities));

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
}
