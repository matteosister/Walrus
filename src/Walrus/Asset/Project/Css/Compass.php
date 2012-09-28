<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.35
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project\Css;

use Walrus\Asset\ProjectInterface,
    Walrus\Asset\Project\AbstractProject;
use CompassElephant\CompassProject;
use Symfony\Component\Finder\Finder,
    Symfony\Component\Filesystem\Filesystem;

/**
 * Compass project
 */
class Compass extends AbstractProject implements ProjectInterface
{
    /**
     * @var CompassProject
     */
    private $project;

    /**
     * @var string
     */
    private $name = 'compass';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * class constructor
     *
     * @param CompassProject $project project
     * @param null|string    $name    name
     */
    public function __construct(CompassProject $project, $name = null)
    {
        $this->project = $project;
        if (null !== $name) {
            $this->name = $name;
        }
        $this->filesystem = new Filesystem();
    }

    /**
     * compile a project
     *
     * @return bool
     */
    public function compile()
    {
        $this->project->compile(true);
    }

    /**
     * publish the generate files to the final destination
     *
     * @param null|string $to folder to publish to
     *
     * @return null
     */
    public function publish($to = null)
    {
        $finder = new Finder();
        $iterator = $finder->files()->name('*.css')->in($this->getOutputFolder());
        $this->filesystem->mirror($this->getOutputFolder(), $to, $iterator);
    }

    /**
     * get the output for the header of the page
     *
     * @return string
     */
    public function output()
    {
        $iterator = Finder::create()->files()->name('*.css')->in($this->getOutputFolder());
        $output = '';
        foreach ($iterator as $file) {
            $output .= sprintf('<link rel="stylesheet" type="text/css" href="/%s/%s">', $this->getProjectType(), $file->getRelativePathName());
        }

        return $output;
    }

    /**
     * output folder of compass compile
     *
     * @return string
     */
    private function getOutputFolder()
    {
        return $this->project->getProjectPath().DIRECTORY_SEPARATOR.'stylesheets';
    }

    /**
     * project name getter
     *
     * @return string
     */
    public function getName()
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
