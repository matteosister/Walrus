<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 17.19
 *
 * Just for fun...
 */

namespace Walrus\Twig\Extension;

use Walrus\Twig\Extension\WalrusExtension,
    Walrus\WalrusTestCase;

class WalrusExtensionTest extends WalrusTestCase
{
    public function testConstructor()
    {
        $ext = new WalrusExtension($this->getMockConfiguration(), $this->getMockAssetCollection());
        $this->assertInstanceOf('\Twig_Extension', $ext);
    }
}
