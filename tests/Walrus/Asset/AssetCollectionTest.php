<?php
/**
 * User: matteo
 * Date: 25/09/12
 * Time: 14.21
 *
 * Just for fun...
 */

namespace Walrus\Asset;

use Walrus\Asset\AssetCollection,
    Walrus\WalrusTestCase;

class AssetCollectionTest extends WalrusTestCase
{
    /**
     * @group assets
     */
    public function testConstruct()
    {
        $ac = new AssetCollection();
        $this->assertInstanceOf('\Countable', $ac);
        $this->assertInstanceOf('\ArrayAccess', $ac);
        $this->assertInstanceOf('\Iterator', $ac);
        $this->assertEmpty($ac->count());
    }

    /**
     * @group assets
     */
    public function testAddProject()
    {
        $ac = new AssetCollection();
        $mockProject = $this->getMock('Walrus\Asset\Project\Css\CssFolder', array(), array(''));
        $ac->addProject($mockProject);
        $this->assertCount(1, $ac);
        $this->assertEquals($mockProject, $ac[0]);
        foreach ($ac as $project) {
            $this->assertNotNull($project);
        }
        $ac[0] = null;
        $this->assertEquals(null, $ac[0]);
        $ac[] = $mockProject;
        $this->assertCount(1, $ac);
    }
}
