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
use Walrus\MDObject\Page\Page;

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
        $this->pageFileExists('0001-test');
        $page = new Page(file_get_contents($this->pagesDir.'/0001-test.md'));
        $this->generatedPages[] = $page;
        $this->assertTrue($page->homepage);

        $tester->execute(array('command' => $command->getName(), 'title' => 'test2', 'parent' => 'test'));
        $this->pageFileExists('0002-test2');
        $page2 = new Page(file_get_contents($this->pagesDir.'/0002-test2.md'));
        $this->generatedPages[] = $page2;
        $this->assertFalse($page2->homepage);
    }
}
