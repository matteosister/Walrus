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
use LessElephant\LessProject;

/**
 * Less project
 */
class Less extends AbstractProject implements ProjectInterface
{
    /**
     * @var \LessElephant\LessProject
     */
    private $project;

    /**
     * @var string
     */
    private $name;

    /**
     * class constructor
     *
     * @param LessProject $project LessProject instace
     */
    public function __construct(LessProject $project, $name = null)
    {
        $this->project = $project;
        if (null !== $name) {
            $this->name = $name;
        }
    }

    /**
     * compile a project
     */
    public function compile()
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
    public function publish($to = null)
    {
        // nothing to do here, less publish to the final destination
        // TODO: assetic?
    }

    /**
     * get the output for the header of the page
     *
     * @return string
     */
    public function output()
    {
        $pathInfo = pathinfo($this->project->getDestination());

        return sprintf('<link rel="stylesheet" type="text/css" href="/%s/%s">', $this->getProjectType(), $pathInfo['basename']);
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
