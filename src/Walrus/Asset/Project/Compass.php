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

class Compass implements ProjectInterface
{
    /**
     * @var CompassProject
     */
    private $project;

    /**
     * class constructor
     *
     * @param CompassProject $project project
     */
    public function __construct(CompassProject $project)
    {
        $this->project = $project;
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
}
