<?php
/**
 * User: matteo
 * Date: 17/10/12
 * Time: 22.42
 *
 * Just for fun...
 */

namespace Walrus\Command;

use Walrus\WalrusTestCase,
    Symfony\Component\Console\Tester\CommandTester,
    Walrus\Asset\Project\AbstractProject;

class GenerateSiteCommandTest extends WalrusTestCase
{
    public function testExecuteEmpty()
    {
        $publicDir = $this->playgroundDir.'/public';
        $this->filesystem->mkdir($publicDir);
        $application = $this->getApplication();
        $command = $application->find('generate:site');
        $tester = new CommandTester($command);
        $tester->execute(array('command' => $command->getName()));
        $this->assertFileExists($publicDir.'/'.AbstractProject::TYPE_CSS);
        $this->assertFileExists($publicDir.'/'.AbstractProject::TYPE_JS);
        $this->assertFileNotExists($publicDir.'/index.html');
    }

    /*public function testExecuteWithPage()
    {
        $this->addRandomPages(1);
        $publicDir = $this->playgroundDir.'/public';
        $this->filesystem->mkdir($publicDir);
        $application = $this->getApplication();
        $command = $application->find('generate:site');
        $tester = new CommandTester($command);
        $tester->execute(array('command' => $command->getName()));
        $this->assertFileExists($publicDir.'/'.AbstractProject::TYPE_CSS);
        $this->assertFileExists($publicDir.'/'.AbstractProject::TYPE_JS);
        $this->assertFileExists($publicDir.'/index.html');
    }*/
}
