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

class CssFolder implements ProjectInterface
{
    /**
     * @var array
     */
    private $folders;

    /**
     * Class constructor
     */
    public function __construct($folders)
    {
        $this->folders = $folders;
    }

    /**
     * compile a project
     *
     * @return bool
     */
    function compile()
    {
    }

    /**
     * publish the generate files to the final destination
     *
     * @param null $to publish to
     *
     * @return null
     */
    function publish($to = null)
    {

        $iterator = Finder::create()->files()->name('*.css')->in($this->folders);
        if (iterator_count($iterator)) {
            foreach ($iterator as $file) {
                copy($file->getRealPath(), sprintf('%s/%s', $to, $file->getRelativePathname()));
            }
        }
    }
}
