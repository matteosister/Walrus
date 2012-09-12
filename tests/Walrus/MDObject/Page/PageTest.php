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
    public function testConstructor()
    {
        $page = new Page($this->getMDPageContent());
        $metadata = $page->getMetadata();
        $content = $page->getContent();
        $this->assertInstanceOf('Walrus\MDObject\Page\Metadata', $metadata);
        $this->assertInstanceOf('Walrus\MDObject\Page\Content', $content);
        $this->assertEquals(static::PAGE_TITLE, $metadata->getTitle());
        $this->assertInstanceOf('\DateTime', $metadata->getDate());
        $this->assertEquals(static::PAGE_DATE, $metadata->getDate()->format(static::DATE_FORMAT));
        $this->assertEquals(static::PAGE_HOMEPAGE, $metadata->getHomepage());
        $this->assertEquals(static::PAGE_URL, $metadata->getUrl());
        $this->assertEquals(static::PAGE_TYPE, $metadata->getType());
        $this->assertEquals(static::PAGE_CONTENT, $content->getMd());
    }
}
