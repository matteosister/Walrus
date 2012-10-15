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
    public function testLoad()
    {
        $this->addRandomPages(2);
        $pageCollection = new PageCollection();
        $pageCollection->load($this->pagesDir);
        $this->assertCount(2, $pageCollection);
        $this->assertInstanceOf('\ArrayAccess', $pageCollection);
        $this->assertInstanceOf('\Countable', $pageCollection);
        $this->assertInstanceOf('\Iterator', $pageCollection);
        foreach ($pageCollection as $page) {
            $this->assertInstanceOf('Walrus\MDObject\Page\Page', $page);
        }
        $this->assertInternalType('array', $pageCollection->toArray());
        $this->iteratorTest(new PageCollection());
    }

    /**
     * @group collections
     */
    public function testEmptyDir()
    {
        $pageCollection = new PageCollection();
        $this->assertNull($pageCollection->load($this->playgroundDir.'/non-existent'));
    }
}
