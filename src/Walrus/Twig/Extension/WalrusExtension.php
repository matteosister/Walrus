<?php
/**
 * User: matteo
 * Date: 13/09/12
 * Time: 22.03
 *
 * Just for fun...
 */

namespace Walrus\Twig\Extension;

use Walrus\DI\Configuration;
use Symfony\Component\Finder\Finder;

class WalrusExtension extends \Twig_Extension
{
    /**
     * @var \Walrus\DI\Configuration
     */
    private $configuration;

    /**
     * class constructor
     *
     * @param \Walrus\DI\Configuration $configuration configuration instance
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getFunctions()
    {
        return array(
            'stylesheets' => new \Twig_Function_Method($this, 'stylesheets')
        );
    }

    public function stylesheets()
    {

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'walrus';
    }
}
