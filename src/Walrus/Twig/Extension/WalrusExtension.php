<?php
/**
 * User: matteo
 * Date: 13/09/12
 * Time: 22.03
 *
 * Just for fun...
 */

namespace Walrus\Twig\Extension;

use Walrus\DI\Configuration,
    Walrus\Asset\Collection;

use Symfony\Component\Finder\Finder;

class WalrusExtension extends \Twig_Extension
{
    /**
     * @var \Walrus\DI\Configuration
     */
    private $configuration;

    /**
     * @var \Walrus\Asset\Collection
     */
    private $assetCollection;

    /**
     * class constructor
     *
     * @param \Walrus\DI\Configuration $configuration configuration instance
     */
    public function __construct(Configuration $configuration, Collection $assetCollection)
    {
        $this->configuration = $configuration;
        $this->assetCollection = $assetCollection;
    }

    public function getFunctions()
    {
        return array(
            'stylesheets' => new \Twig_Function_Method($this, 'stylesheets', array('is_safe' => array('all')))
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
