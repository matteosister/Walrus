<?php
/**
 * User: matteo
 * Date: 12/09/12
 * Time: 10.17
 *
 * Just for fun...
 */

namespace Walrus\Collection;

use Walrus\Collection\Collection;


/**
 * Class representing a collection of pages
 */
class PageCollection extends Collection
{
    /**
     * class constructor
     *
     * @param string $type collection type
     */
    public function __construct($type)
    {
        parent::__construct();
        $this->type = $type;
    }
}
