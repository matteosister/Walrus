<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.37
 *
 * Just for fun...
 */

namespace Walrus\Asset;

/**
 * Interface for an asset project
 */
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
     * @param null $to     publish to
     * @param null $filter FilterInterface
     *
     * @return null
     */
    function publish($to = null, $filter = null);

    /**
     * get the output for the header of the page
     *
     * @return string
     */
    function output();

    /**
     * project name getter
     *
     * @return string
     */
    function getName();
}
