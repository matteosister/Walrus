<?php
/**
 * User: matteo
 * Date: 25/09/12
 * Time: 15.12
 *
 * Just for fun...
 */

namespace Walrus\Configuration;

use Walrus\Configuration\ThemeConfiguration,
    Walrus\WalrusTestCase;

class ThemeConfigurationTest extends WalrusTestCase
{
    /**
     * @group di
     */
    public function testGetConfigTreeBuilder()
    {
        $themeConfiguration = new ThemeConfiguration();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $themeConfiguration->getConfigTreeBuilder());
    }
}
