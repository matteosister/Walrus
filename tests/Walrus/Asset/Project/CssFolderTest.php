<?php
/**
 * User: matteo
 * Date: 25/09/12
 * Time: 14.32
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project;

use Walrus\Asset\Project\Css\CssFolder,
    Walrus\WalrusTestCase;

class CssFolderTest extends WalrusTestCase
{
    /**
     * @group assets
     */
    public function testConstruct()
    {
        $fileContent = 'body { background-color: #000 }';
        $filename = $this->cssSourceProjectDir.'/main.css';
        $this->filesystem->touch($filename);
        file_put_contents($filename, $fileContent);

        $p = new CssFolder($this->cssSourceProjectDir, 'cssfolder-test-project');
        $this->assertEquals('cssfolder-test-project', $p->getName());
        $p->publish($this->assetsProjectsPublishDir);
        $this->assertFileExists($this->assetsProjectsPublishDir.'/'.$p->getOutputFilename());
        $this->assertEquals($fileContent, file_get_contents($this->assetsProjectsPublishDir.'/'.$p->getOutputFilename()));
    }
}
