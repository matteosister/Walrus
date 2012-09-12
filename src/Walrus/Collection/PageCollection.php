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
class PageCollection implements \ArrayAccess, \Countable
{
    /**
     * @var Symfony\Component\Finder\Finder
     */
    private $iterator;

    /**
     * @var array
     */
    private $pages;

    /**
     * load md files from a folder
     *
     * @param $dir
     *
     * @return bool|int
     * @throws \Walrus\Exception\NoPagesCreated
     */
    public function load($dir)
    {
        if (!is_dir($dir)) {
            throw new NoPagesCreated();
        }
        $finder = new Finder();
        $this->iterator = $finder->files()->in($dir);
        $this->generate();
    }

    /**
     * generate the collection
     */
    public function generate()
    {
        foreach ($this->iterator as $md) {
            $md = file_get_contents($md->getRealPath());
            $this->pages[] = new Page($md);
        }
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
        return isset($this->pages[$offset]);
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
        return isset($this->pages[$offset]) ? $this->pages[$offset] : null;
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
            $this->pages[] = $value;
        } else {
            $this->pages[$offset] = $value;
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
        unset($this->container[$offset]);
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
        return count($this->pages);
    }
}
