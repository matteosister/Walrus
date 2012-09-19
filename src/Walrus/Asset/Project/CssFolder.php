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

class CssFolder implements ProjectInterface
{
    /**
     * @var string
     */
    private $folder;

    /**
     * Class constructor
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
        $iterator = Finder::create()->files()->name('*.css')->in($this->folder);
        if (iterator_count($iterator)) {
            foreach ($iterator as $file) {
                $fs = new Filesystem();
                $fs->copy($file->getRealPath(), sprintf('%s/%s', $to, $file->getRelativePathname()));
            }
        }
    }
}
