<?php
/**
 * User: matteo
 * Date: 12/09/12
 * Time: 12.15
 *
 * Just for fun...
 */

namespace Walrus\DI;

use Walrus\DI\Configuration,
    Walrus\WalrusTestCase;

class ConfigurationTest extends WalrusTestCase
{
    /**
     * @group di
     */
    public function testSetterGetter()
    {
        $conf = new Configuration();
        $val = 'test';
        $conf->set('test', $val);
        $this->assertEquals($val, $conf->get('test'));
        $val = new \DateTime();
        $conf->set('test', $val);
        $this->assertEquals($val, $conf->get('test'));
    }

    /**
     * @expectedException Walrus\Exception\MissingConfigurationParameter
     * @group di
     */
    public function testError()
    {
        $conf = new Configuration();
        $conf->get('non-existent');
    }
}
