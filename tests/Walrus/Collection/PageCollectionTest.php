<?php
/**
 * User: matteo
 * Date: 12/09/12
 * Time: 10.55
 *
 * Just for fun...
 */

namespace Walrus\Collection;

use Walrus\Collection\PageCollection,
    Walrus\WalrusTestCase;

class PageCollectionTest extends WalrusTestCase
{
    public function testFullDir()
    {
        $this->addRandomPages(3);
        $pageCollection = new PageCollection(Collection::TYPE_PAGES);
        $pageCollection->load($this->pagesDir);
        $this->assertInstanceOf('\ArrayAccess', $pageCollection);
        $this->assertInstanceOf('\Countable', $pageCollection);
        $this->assertCount(3, $pageCollection);
    }

    /**
     * @expectedException Walrus\Exception\NoPagesCreated
     */
    public function testEmptyDir()
    {
        $pageCollection = new PageCollection(Collection::TYPE_PAGES);
        $pageCollection->load($this->playgroundDir.'/non-existent');
    }
}
