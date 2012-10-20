<?php
/**
 * User: matteo
 * Date: 21/10/12
 * Time: 0.43
 *
 * Just for fun...
 */

namespace Walrus\Exception;

class MultipleThemeFoldersException extends \Exception
{
    protected $message = "Multiple theme folder founded with the same theme inside. It was impossible to define the right theme to use. Leave only one folder in your project, or set the \"theme_location\" property in your walrus.yml file";
}
