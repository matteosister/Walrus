<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.34
 *
 * Just for fun...
 */

namespace Walrus\Asset;

use Walrus\Asset\ProjectInterface;

class Collection implements \Countable
{
    /**
     * @var array
     */
    private $projects;

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->projects = array();
    }

    /**
     * add a project to the internal collection
     *
     * @param ProjectInterface $project a project
     */
    public function addProject(ProjectInterface $project)
    {
        $this->projects[] = $project;
    }

    /**
     * Compile all projects
     */
    public function compile()
    {
        foreach($this->projects as $project) {
            $project->compile();
        }
    }

    /**
     * publish all projects
     *
     * @param string $to destination folder
     */
    public function publish($to)
    {
        foreach($this->projects as $project) {
            $project->publish($to);
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->projects);
    }
}
