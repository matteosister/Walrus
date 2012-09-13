<?php
/**
 * User: matteo
 * Date: 13/09/12
 * Time: 22.03
 *
 * Just for fun...
 */

namespace Walrus\Extension;

class WalrusExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'test' => new \Twig_Filter_Method($this, 'test', array('is_safe' => array('all')))
        );
    }

    public function test()
    {
        return 'pippo';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'walrus';
    }
}
