<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.35
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project;

use Walrus\Asset\ProjectInterface;
use CompassElephant\CompassProject;
use Symfony\Component\Finder\Finder;

/**
 * Compass project
 */
class Compass implements ProjectInterface
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
     * class constructor
     *
     * @param CompassProject $project project
     */
    public function __construct(CompassProject $project, $name = null)
    {
        $this->project = $project;
        if (null !== $name) {
            $this->name = $name;
        }
    }

    /**
     * compile a project
     *
     * @return bool
     */
    function compile()
    {
        $this->project->compile(true);
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
        $finder = new Finder();
        $folder = $this->project->getProjectPath().DIRECTORY_SEPARATOR.'stylesheets';
        $iterator = $finder->files()->name('*.css')->in($folder);
        foreach ($iterator as $file) {
            copy($file->getRealPath(), $to.'/'.$file->getRelativePathname());
        }
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
}
