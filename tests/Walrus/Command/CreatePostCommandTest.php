<?php
/**
 * User: matteo
 * Date: 12/09/12
 * Time: 17.20
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Walrus\Command\CreatePostCommand,
    Walrus\WalrusTestCase;

use Symfony\Component\Console\Tester\CommandTester;

class CreatePostCommandTest extends WalrusTestCase
{
    public function testExecute()
    {
        $application = $this->getApplication();
        $command = $application->find('create:post');
        $tester = new CommandTester($command);
        $tester->execute(array('command' => $command->getName(), 'title' => 'test'));

        $this->assertFileExists(__DIR__.'/../Playground/drafting/posts/2012-01-01_10:00:00_post_test.md');
    }
}
