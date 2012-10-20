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
}
