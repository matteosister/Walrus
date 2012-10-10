<?php
/**
 * User: matteo
 * Date: 06/09/12
 * Time: 16.47
 *
 * Just for fun...
 */

namespace Walrus\MDObject\Page;

use Walrus\MDObject\BaseObject,
    Walrus\Utilities\CamelCaseTrait,
    Walrus\Exception\MetadataMissing;

/**
 * Page Object
 */
class Page extends BaseObject
{
    use CamelCaseTrait;

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
        $this->checkPage($md);
        $this->metadata = $this->parseMetadata($md, 'Walrus\MDObject\Page\Metadata');
        $this->content = $this->parseContent($md, 'Walrus\MDObject\Page\Content');
    }

    /**
     * try to call method on the metadata. Useful for twig
     *
     * @param string $name      method name
     *
     * @throws \Walrus\Exception\MetadataMissing
     * @return mixed
     */
    function __get($name)
    {
        $methodName = 'get'.$this->toCamelCase($name, true);
        if (is_callable(array($this->metadata, $methodName))) {
            return $this->metadata->$methodName();
        } else {
            throw new MetadataMissing();
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getMetadata()->getUrl();
    }


    /**
     * toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->metadata->getTitle();
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