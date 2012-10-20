<?php
/**
 * User: matteo
 * Date: 17/10/12
 * Time: 9.58
 *
 * Just for fun...
 */

namespace Walrus\Theme;

use Walrus\WalrusTestCase,
    Walrus\Theme\Theme;

class ThemeTest extends WalrusTestCase
{
    public function testConstructor()
    {
        $t = new Theme($this->getMockAssetCollection());
        $t->setThemePath($this->fixturesDir.'/themes/test1');
        $this->assertEquals('test1', $t->getName());
        $this->assertFalse($t->getCompressAssets());
        $this->assertNull($t->getImages());
        $t->setThemePath($this->fixturesDir.'/themes/test2');
        $this->assertEquals('test2', $t->getName());
        $this->assertTrue($t->getCompressAssets());
    }
}
