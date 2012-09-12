<?php
/**
 * User: matteo
 * Date: 12/09/12
 * Time: 17.29
 *
 * Just for fun...
 */

namespace Walrus\Collection;

use Walrus\Collection\Collection;

/**
 * Class representing a collection of posts
 */
class PostCollection extends Collection
{
    /**
     * class constructor
     *
     * @param string $type collection type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }
}
