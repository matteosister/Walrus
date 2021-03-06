<?php
/**
 * User: matteo
 * Date: 28/09/12
 * Time: 15.04
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project;

/**
 * Abstract asset project
 */
abstract class AbstractProject
{
    const TYPE_CSS = '__css';
    const TYPE_JS = '__js';

    /**
     * @var bool
     */
    protected $compress;

    /**
     * @return string
     */
    abstract public function getProjectType();

    /**
     * Compress setter
     *
     * @param boolean $compress compress var
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }
}
