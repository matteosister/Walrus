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
     * @var string
     */
    private $url;

    /**
     * @var bool
     */
    private $hasChildren;

    /**
     * @var string
     */
    private $mdPath;

    /**
     * constructor
     *
     * @param string $mdPath the .md full file path
     */
    public function __construct($mdPath)
    {
        $this->mdPath = $mdPath;
        $md = file_get_contents($mdPath);
        $this->checkPage($md);
        $this->metadata = $this->parseMetadata($md, 'Walrus\MDObject\Page\Metadata');
        $this->content = $this->parseContent($md, 'Walrus\MDObject\Page\Content');
    }

    /**
     * try to call method on the metadata. Useful for twig
     *
     * @param string $name property name
     *
     * @throws \Walrus\Exception\MetadataMissing
     * @return mixed
     */
    public function __get($name)
    {
        $methodName = 'get'.$this->toCamelCase($name, true);
        if (is_callable(array($this->metadata, $methodName))) {
            return $this->metadata->$methodName();
        } else {
            throw new MetadataMissing($name);
        }
    }

    /**
     * Url setter
     *
     * @param string $url la variabile url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Url getter
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * HasChildren setter
     *
     * @param boolean $hasChildren la variabile hasChildren
     */
    public function setHasChildren($hasChildren)
    {
        $this->hasChildren = $hasChildren;
    }

    /**
     * HasChildren getter
     *
     * @return boolean
     */
    public function getHasChildren()
    {
        return $this->hasChildren;
    }

    /**
     * MdPath setter
     *
     * @param string $mdPath la variabile mdPath
     */
    public function setMdPath($mdPath)
    {
        $this->mdPath = $mdPath;
    }

    /**
     * MdPath getter
     *
     * @return string
     */
    public function getMdPath()
    {
        return $this->mdPath;
    }
}