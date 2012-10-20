<?php
/**
 * User: matteo
 * Date: 16/10/12
 * Time: 23.07
 *
 * Just for fun...
 */

namespace Walrus\Project;

use Walrus\WalrusTestCase,
    Walrus\Project\Project;

/**
 * project test
 */
class ProjectTest extends WalrusTestCase
{
    public function testConstructor()
    {
        $this->filesystem->copy($this->getFixtureFile('walrus.yml'), $this->playgroundDir.'/walrus.yml');
        $project = new Project($this->playgroundDir, $this->getMockTheme());
        $this->assertEquals('Test', $project->getSiteName());
        $this->assertNull($project->getThemeLocation());
    }

    public function testCustomConfigurationFile()
    {
        $this->filesystem->copy($this->getFixtureFile('walrus.yml'), $this->playgroundDir.'/test.yml');
        $project = new Project($this->playgroundDir, $this->getMockTheme(), 'test.yml');
        $this->assertEquals('Test', $project->getSiteName());
        $this->assertNull($project->getThemeLocation());
    }

    /**
     * @expectedException \Walrus\Exception\ThemeFolderNotFound
     */
    public function testAutoLoadThemesNotFound()
    {
        $this->filesystem->copy($this->getFixtureFile('walrus.yml'), $this->playgroundDir.'/walrus.yml');
        $project = new Project($this->playgroundDir, $this->getMockTheme());
        $project->buildTheme();
    }

    /**
     * @expectedException \Walrus\Exception\MultipleThemeFoldersException
     */
    public function testAutoLoadThemesMultiple()
    {
        $this->filesystem->copy($this->getFixtureFile('walrus.yml'), $this->playgroundDir.'/walrus.yml');
        $project = new Project($this->playgroundDir, $this->getMockTheme());
        $this->filesystem->mkdir(array($this->playgroundDir.'/theme1', $this->playgroundDir.'/theme2'));
        $this->filesystem->mirror($this->fixturesDir.'/themes/test1', $this->playgroundDir.'/theme1');
        $this->filesystem->mirror($this->fixturesDir.'/themes/test1', $this->playgroundDir.'/theme2');
        $project->buildTheme();
    }

    public function testAutoLoadThemes()
    {
        $this->filesystem->copy($this->getFixtureFile('walrus.yml'), $this->playgroundDir.'/walrus.yml');
        $project = new Project($this->playgroundDir, $this->getMockTheme());
        $this->filesystem->mkdir(array($this->playgroundDir.'/theme1', $this->playgroundDir.'/theme2'));
        $this->filesystem->mirror($this->fixturesDir.'/themes/test1', $this->playgroundDir.'/theme2');
        $project->buildTheme();
        $this->assertEquals($this->playgroundDir.'/theme2', $project->getTheme()->getThemePath());
        $this->assertEquals('test1', $project->getTheme()->getName());
    }
}
