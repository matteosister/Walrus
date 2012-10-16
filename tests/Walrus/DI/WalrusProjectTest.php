<?php
/**
 * User: matteo
 * Date: 16/10/12
 * Time: 17.48
 *
 * Just for fun...
 */

namespace Walrus\DI;

use Walrus\WalrusTestCase;
use Walrus\DI\WalrusProject;

class WalrusProjectTest extends WalrusTestCase
{
    public function testGetApplication()
    {
        $wp = new WalrusProject($this->playgroundDir);
        $commands = array('startup:project', 'create:page', 'generate:site', 'startup:server', 'project:watch');
        foreach ($commands as $commandName) {
            $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $wp->getApplication()->find($commandName));
        }
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $wp->getContainer());
    }
}
