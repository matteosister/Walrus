<?php
/**
 * User: matteo
 * Date: 07/09/12
 * Time: 15.42
 *
 * Just for fun...
 */

namespace Walrus\MDObject\Page;

use dflydev\markdown\MarkdownParser;

/**
 * Post content
 */
class Content
{
    /**
     * @var string
     */
    private $md;

    /**
     * @param string $md the content part of a post object
     */
    public function __construct($md)
    {
        $this->md = trim($md);
    }

    /**
     * Md setter
     *
     * @param string $md la variabile md
     */
    public function setMd($md)
    {
        $this->md = $md;
    }

    /**
     * Md getter
     *
     * @return string
     */
    public function getMd()
    {
        return $this->md;
    }

    /**
     * get the html code
     */
    public function getHtml()
    {
        $parser = new MarkdownParser();

        return $parser->transform($this->md);
    }
}
