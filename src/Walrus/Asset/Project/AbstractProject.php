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
     * @return string
     */
    abstract function getProjectType();
}
