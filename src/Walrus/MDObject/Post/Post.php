<?php
/**
 * User: matteo
 * Date: 03/09/12
 * Time: 16.47
 *
 * Just for fun...
 */

namespace Walrus\MDObject\Post;

use dflydev\markdown\MarkdownParser;
use Walrus\MDObject\Post\Metadata,
    Walrus\MDObject\Post\Content,
    Walrus\MDObject\BaseObject;

/**
 * Post class
 */
class Post extends BaseObject
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
     * class constructor
     *
     * @param string $md the markdown content
     *
     * @throws \Walrus\Exception\PostParseException
     */
    public function __construct($md)
    {
        $this->checkPost($md);
        $this->metadata = $this->parseMetadata($md, 'Walrus\MDObject\Post\Metadata');
        $this->content = $this->parseContent($md, 'Walrus\MDObject\Post\Content');
    }

    /**
     * Metadata setter
     *
     * @param \Walrus\MDObject\Post\Metadata $metadata metadata property
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Metadata getter
     *
     * @return \Walrus\MDObject\Post\Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Content setter
     *
     * @param \Walrus\MDObject\Post\Content $content la variabile content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Content getter
     *
     * @return \Walrus\MDObject\Post\Content
     */
    public function getContent()
    {
        return $this->content;
    }
}
