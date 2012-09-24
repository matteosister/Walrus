<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.37
 *
 * Just for fun...
 */

namespace Walrus\Asset;

interface ProjectInterface
{
    /**
     * compile a project
     *
     * @return bool
     */
    function compile();

    /**
     * publish the generate files to the final destination
     *
     * @param null $to publish to
     *
     * @return null
     */
    function publish($to = null);

    /**
     * project name getter
     *
     * @return string
     */
    function getName();
}
