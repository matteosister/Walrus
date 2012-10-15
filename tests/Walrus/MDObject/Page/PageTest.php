<?php
/**
 * User: matteo
 * Date: 11/09/12
 * Time: 11.27
 *
 * Just for fun...
 */

namespace Walrus\MDObject\Page;

use Walrus\MDObject\Page\Page;
use Walrus\WalrusTestCase;

class PageTest extends WalrusTestCase
{
    /**
     * @group md
     */
    public function testConstructor()
    {
        $page = new Page($this->getFixtureFile('homepage.md'));
        $metadata = $page->getMetadata();
        $content = $page->getContent();
        $this->assertInstanceOf('Walrus\MDObject\Page\Metadata', $metadata);
        $this->assertInstanceOf('Walrus\MDObject\Page\Content', $content);
    }
}
