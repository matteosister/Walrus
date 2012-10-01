<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 17.19
 *
 * Just for fun...
 */

namespace Walrus\Twig\Extension;

use Walrus\Twig\Extension\ThemeExtension,
    Walrus\WalrusTestCase;

class ThemeExtensionTest extends WalrusTestCase
{
    /**
     * @group twig
     */
    public function testConstructor()
    {
        $ext = new ThemeExtension(
            $this->getMockConfiguration(),
            $this->getMockAssetCollection(),
            $this->getMockPageCollection()
        );
        $this->assertInstanceOf('\Twig_Extension', $ext);
    }
}
