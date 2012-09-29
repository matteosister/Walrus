<?php
/**
 * User: matteo
 * Date: 28/09/12
 * Time: 15.04
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project;

abstract class AbstractProject
{
    const TYPE_CSS = 'stylesheets';
    const TYPE_JS = 'javascripts';

    /**
     * @var bool
     */
    protected $compress;

    /**
     * @return string
     */
    abstract function getProjectType();

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
