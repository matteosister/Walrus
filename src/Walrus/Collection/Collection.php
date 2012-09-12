<?php
/**
 * User: matteo
 * Date: 12/09/12
 * Time: 17.32
 *
 * Just for fun...
 */

namespace Walrus\Collection;

use Walrus\Exception\NoPagesCreated,
    Walrus\Exception\NoPostsCreated,
    Walrus\MDObject\Page\Page,
    Walrus\MDObject\Post\Post;

use Symfony\Component\Finder\Finder;

class Collection implements \ArrayAccess, \Countable
{
    const TYPE_PAGES = 'pages';
    const TYPE_POSTS = 'posts';

    protected $objects;

    /**
     * @var Symfony\Component\Finder\Finder
     */
    private $iterator;

    /**
     * @var string
     */
    protected $type;

    /**
     * load md files from a folder
     *
     * @param $dir
     *
     * @throws \Walrus\Exception\NoPostsCreated
     * @throws \Walrus\Exception\NoPagesCreated
     */
    public function load($dir)
    {
        if (!is_dir($dir)) {
            switch ($this->type)
            {
                case static::TYPE_PAGES:
                    throw new \Walrus\Exception\NoPagesCreated();
                    break;
                case static::TYPE_POSTS:
                    throw new \Walrus\Exception\NoPostsCreated();
                    break;
            }
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
            switch ($this->type)
            {
                case static::TYPE_PAGES:
                    $this->objects[] = new Page($md);
                    break;
                case static::TYPE_POSTS:
                    $this->objects[] = new Post($md);
                    break;
            }
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
}
