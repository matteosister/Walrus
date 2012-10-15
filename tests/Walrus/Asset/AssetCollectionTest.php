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
        $ac = new AssetCollection(false);
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
        $ac = new AssetCollection(false);
        $mockProject = $this->getMockCssFolder();
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

    public function testGetStylesheets()
    {
        $ac = new AssetCollection(true);
        $this->assertCount(0, $ac->getStylesheetProjects());
        $mockCss = $this->getMockCssFolder();
        $ac->addProject($mockCss);
        $this->assertCount(1, $ac->getStylesheetProjects());
        $mockCss2 = $this->getMockCssFolder();
        $ac->addProject($mockCss2);
        $this->assertCount(2, $ac->getStylesheetProjects());
    }

    public function testGetJavascripts()
    {
        $ac = new AssetCollection(true);
        $this->assertCount(0, $ac->getJavascriptProjects());
        $mockJs = $this->getMockJsFolder();
        $ac->addProject($mockJs);
        $this->assertCount(1, $ac->getJavascriptProjects());
        $mockJs2 = $this->getMockJsFolder();
        $ac->addProject($mockJs2);
        $this->assertCount(2, $ac->getJavascriptProjects());
    }


}
