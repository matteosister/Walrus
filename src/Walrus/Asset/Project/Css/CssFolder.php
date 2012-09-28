<?php
/**
 * User: matteo
 * Date: 18/09/12
 * Time: 23.17
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project\Css;

use Walrus\Asset\ProjectInterface,
    Walrus\Asset\Project\AbstractProject;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * CssFolder project
 */
class CssFolder extends AbstractProject implements ProjectInterface
{
    /**
     * @var string
     */
    private $folder;

    /**
     * @var string
     */
    private $name = 'css folder';

    /**
     * Class constructor
     *
     * @param string $folder folder
     * @param null   $name   project name
     */
    public function __construct($folder, $name = null)
    {
        $this->folder = $folder;
        if (null !== $name) {
            $this->name = $name;
        }
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

    /**
     * get the output for the header of the page
     *
     * @return string
     */
    public function output()
    {
        $iterator = Finder::create()->files()->name('*.css')->in($this->folder);
        $output = '';
        foreach ($iterator as $file) {
            $output .= sprintf('<link rel="stylesheet" type="text/css" href="/%s/%s" media="screen, projection">', $this->getProjectType(), $file->getRelativePathName());
        }

        return $output;
    }

    /**
     * project name getter
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    function getProjectType()
    {
        return static::TYPE_CSS;
    }
}
