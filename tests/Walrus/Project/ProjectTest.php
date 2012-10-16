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
        $project = new Project($this->playgroundDir);
        $this->assertEquals('Test', $project->getSiteName());
        $this->assertEquals('test', $project->getTheme());
        $this->assertNull($project->getThemeLocation());
    }
}
