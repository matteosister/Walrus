<?php
/**
 * User: matteo
 * Date: 12/09/12
 * Time: 10.17
 *
 * Just for fun...
 */

namespace Walrus\Collection;

use Walrus\Exception\NoPagesCreated,
    Walrus\MDObject\Page\Page;

use Symfony\Component\Finder\Finder;

/**
 * Class representing a collection of pages
 */
class PageCollection implements \ArrayAccess, \Countable, \Iterator
{
    /**
     * @var array
     */
    protected $objects;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $iterator;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var integer
     */
    private $position;

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->objects = array();
        $this->position = 0;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->objects == null ? array() : $this->objects;
    }

    /**
     * load md files from a folder
     *
     * @param string $dir directory to search for...
     *
     * @throws \Walrus\Exception\NoPagesCreated
     * @return void
     */
    public function load($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $finder = new Finder();
        $this->iterator = $finder->files()->name('*.md')->in($dir);
        foreach ($this->iterator as $mdFile) {
            $this->objects[] = new Page($mdFile->getRealPath());
        }
        foreach ($this->objects as $page) {
            $page->setUrl($this->generateFullUrl($page->getMetadata()->getUrl()));
            $page->setHasChildren(0 !== count($this->getChildrenOf($page->getMetadata()->getUrl())));
        }
        $objects = $this->objects;
        usort($objects, array($this, 'sortObjects'));
        $this->objects = $objects;
    }

    /**
     * sort objects by filename
     *
     * @param Page $a page 1
     * @param Page $b page 2
     *
     * @return int
     */
    private function sortObjects($a, $b)
    {
        $file1 = $a->getMdPath();
        $file2 = $b->getMdPath();
        return $a->getMdPath() > $b->getMdPath() ? 1 : -1;
    }

    /**
     * get all the pages children of homepage
     *
     * @return array
     */
    public function getMainNavigation()
    {
        $homepage = $this->getHomepage();
        if (null === $homepage) {
            return array();
        }
        $homepageUrl = $homepage->getMetadata()->getUrl();
        return $this->getChildrenOf($homepageUrl);
    }

    public function getBreadcrumbs($url)
    {
        $page = $this->findOneByUrl($url);
        $output[] = $page;
        while (null !== $page->getMetadata()->getParent()) {
            $page = $this->findOneByUrl($page->getMetadata()->getParent());
            $output[] = $page;
        }
        return array_reverse($output);
    }

    /**
     * get children of a page by url
     *
     * @param string $url url
     *
     * @return array
     */
    public function getChildrenOf($url)
    {
        return array_filter($this->objects, function(Page $page) use ($url) {
            return $page->parent == $url;
        });
    }

    /**
     * get the homepage
     *
     * @return null|Page
     */
    public function getHomepage()
    {
        foreach ($this->objects as $page) {
            if ($page->homepage) {
                return $page;
            }
        }
        return null;
    }

    /**
     * get a page by it's url
     *
     * @param string $url the url
     *
     * @return null|Page
     */
    public function findOneByUrl($url)
    {
        foreach ($this->objects as $page) {
            if ($url === $page->getMetadata()->getUrl()) {
                return $page;
            }
        }
        return null;
    }

    /**
     * full url
     */
    public function generateFullUrl($url)
    {
        $page = $this->findOneByUrl($url);
        while (null !== $page) {
            $page = $this->findOneByUrl($page->parent);
            if (null === $page) {
                break;
            }
            if ($page->getMetadata()->getHomepage()) {
                break;
            }
            $url = $page->getMetadata()->getUrl().'/'.$url;
        }
        return $url.'.html';
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     * </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     *       The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->objects[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->objects[$offset]) ? $this->objects[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     * </p>
     * @param mixed $value  <p>
     *                      The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->objects[] = $value;
        } else {
            $this->objects[$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->objects[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->objects);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->objects[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++ $this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->objects[$this->position]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }
}
