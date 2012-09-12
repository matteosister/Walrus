<?php
/**
 * User: matteo
 * Date: 03/09/12
 * Time: 16.49
 *
 * Just for fun...
 */

namespace Walrus\MDObject\Page;

use Walrus\MDObject\BaseMetadata;

/**
 * Metadata class
 */
class Metadata extends BaseMetadata
{
    const TYPE_CMS = 'cms';

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $parent;

    /**
     * @var string
     */
    protected $homepage;

    /**
     * @var string
     */
    protected $type;

    /**
     * constructor
     *
     * @param string $md the raw content from the .md file
     */
    public function __construct($md)
    {
        $this->parseLines(preg_split('/\n/', trim($md)));
        $this->date = \DateTime::createFromFormat('Y-m-d_H:i:s', $this->date);
    }

    /**
     * Date setter
     *
     * @param \DateTime $date la variabile date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Date getter
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Title setter
     *
     * @param string $title la variabile title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Title getter
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Homepage setter
     *
     * @param string $homepage la variabile homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * Homepage getter
     *
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Parent setter
     *
     * @param string $parent la variabile parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Parent getter
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Type setter
     *
     * @param string $type la variabile type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Type getter
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
