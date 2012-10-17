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
        $t = new Theme($this->fixturesDir.'/themes/test1', $this->getMockAssetCollection());
        $this->assertEquals($t->getName(), 'test1');
        $this->assertFalse($t->getCompressAssets());
        $this->assertNull($t->getImages());
    }
}
