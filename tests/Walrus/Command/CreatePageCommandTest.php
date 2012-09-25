<?php
/**
 * User: matteo
 * Date: 05/09/12
 * Time: 0.02
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Walrus\WalrusTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * create:page command test
 */
class CreatePageCommandTest extends WalrusTestCase
{
    /**
     * @group cli
     */
    public function testExecute()
    {
        $application = $this->getApplication();
        $command = $application->find('create:page');
        $tester = new CommandTester($command);
        $tester->execute(array('command' => $command->getName(), 'title' => 'test'));

        $this->assertFileExists(__DIR__.'/../Playground/drafting/pages/2012-01-01_10:00:00_page_test.md');
    }
}
