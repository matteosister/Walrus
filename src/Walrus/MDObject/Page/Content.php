<?php
/**
 * User: matteo
 * Date: 07/09/12
 * Time: 15.42
 *
 * Just for fun...
 */

namespace Walrus\MDObject\Page;

/**
 * Page content
 */
class Content
{
    /**
     * @var string
     */
    private $md;

    /**
     * @param string $md the content part of a page object
     */
    public function __construct($md)
    {
        $this->md = trim($md);
    }

    /**
     * toString magic method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->md;
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
}
