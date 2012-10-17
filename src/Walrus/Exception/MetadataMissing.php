<?php
/**
 * User: matteo
 * Date: 07/10/12
 * Time: 18.50
 *
 * Just for fun...
 */

namespace Walrus\Exception;

/**
 * metadata missing
 */
class MetadataMissing extends \Exception
{
    protected $messageTpl = "the metadata %s doesn't exists";

    /**
     * class constructor
     *
     * @param string    $message  message
     * @param int       $code     code
     * @param Exception $previous previous
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct(sprintf($this->messageTpl, $message), $code, $previous);
    }
}
