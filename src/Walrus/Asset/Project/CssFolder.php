<?php
/**
 * User: matteo
 * Date: 18/09/12
 * Time: 23.17
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project;

use Walrus\Asset\ProjectInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * CssFolder project
 */
class CssFolder implements ProjectInterface
{
    /**
     * @var string
     */
    private $folder;

    /**
     * Class constructor
     *
     * @param string $folder folder
     */
    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    /**
     * compile a project
     *
     * @return bool
     */
    public function compile()
    {
    }

    /**
     * publish the generate files to the final destination
     *
     * @param null $to publish to
     *
     * @return null
     */
    public function publish($to = null)
    {
        $fs = new Filesystem();
        $fs->mirror($this->folder, $to);
    }
}
