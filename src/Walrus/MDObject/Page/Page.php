<?php
/**
 * User: matteo
 * Date: 06/09/12
 * Time: 16.47
 *
 * Just for fun...
 */

namespace Walrus\MDObject\Page;

use Walrus\MDObject\BaseObject;

/**
 * Page Object
 */
class Page extends BaseObject
{
    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @var Content
     */
    private $content;

    /**
     * constructor
     *
     * @param string $md the markdown content
     */
    public function __construct($md)
    {
        $this->checkPost($md);
        $this->metadata = $this->parseMetadata($md, 'Walrus\MDObject\Page\Metadata');
        $this->content = $this->parseContent($md, 'Walrus\MDObject\Page\Content');
    }

    /**
     * Content setter
     *
     * @param \Walrus\MDObject\Page\Content $content la variabile content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Content getter
     *
     * @return \Walrus\MDObject\Page\Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Metadata setter
     *
     * @param \Walrus\MDObject\Page\Metadata $metadata la variabile metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Metadata getter
     *
     * @return \Walrus\MDObject\Page\Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}