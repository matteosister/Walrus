<?php
/**
 * User: matteo
 * Date: 03/09/12
 * Time: 16.49
 *
 * Just for fun...
 */

namespace Walrus\MDObject\Post;

/**
 * Metadata class
 */
class Metadata
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $url;

    /**
     * constructor
     *
     * @param string $md the raw content from the .md file
     */
    public function __construct($md)
    {
        $lines = preg_split('/\n/', trim($md));
        foreach ($lines as $line) {
            $this->parseLine($line);
        }
        $this->date = \DateTime::createFromFormat('y-m-d H:i:s', $this->date);
    }

    /**
     * parse a metadata line
     *
     * @param string $line parse a metadata line
     */
    private function parseLine($line)
    {
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties() as $prop) {
            $propName = $prop->getName();
            $matches = array();
            $regexp = sprintf('/^%s\:\s*(.*)$/', $propName);
            if (preg_match($regexp, $line, $matches)) {
                $this->$propName = $matches[1];
            }
        }
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
}
