<?php
/**
 * User: matteo
 * Date: 03/09/12
 * Time: 17.03
 *
 * Just for fun...
 */

namespace Walrus\Exception;

/**
 * Exception while parsing a page md stream
 */
class PageParseException extends \Exception
{
    protected $message = 'Your page file is not well formatted';
}
