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
use LessElephant\LessProject;

class Less implements ProjectInterface
{
    private $project;

    /**
     * class constructor
     *
     * @param LessProject $project LessProject instace
     */
    public function __construct(LessProject $project)
    {
        $this->project = $project;
    }

    /**
     * compile a project
     *
     * @param bool $force force recompile
     *
     * @return bool
     */
    function compile()
    {
        $this->project->compile();
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
        // nothing to do here, less publish to the final destination
    }
}
