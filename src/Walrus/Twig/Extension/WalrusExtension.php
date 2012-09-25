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
    Walrus\Asset\AssetCollection,
    Walrus\Collection\PageCollection;

use Symfony\Component\Finder\Finder;
use dflydev\markdown\MarkdownParser;

class WalrusExtension extends \Twig_Extension
{
    /**
     * @var \Walrus\DI\Configuration
     */
    private $configuration;

    /**
     * @var \Walrus\Asset\AssetCollection
     */
    private $assetCollection;

    /**
     * class constructor
     *
     * @param \Walrus\DI\Configuration      $configuration   configuration instance
     * @param \Walrus\Asset\AssetCollection $assetCollection assets collection
     */
    public function __construct(
        Configuration $configuration,
        AssetCollection $assetCollection
    )
    {
        $this->configuration = $configuration;
        $this->assetCollection = $assetCollection;
    }

    /**
     * filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'md_to_html'  => new \Twig_Filter_Method($this, 'mdToHtml', array('is_safe' => array('all')))
        );
    }

    /**
     * functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'stylesheets' => new \Twig_Function_Method($this, 'stylesheets', array('is_safe' => array('all')))
        );
    }

    /**
     * stylesheets
     *
     * @return string
     */
    public function stylesheets()
    {
        $out = '';
        foreach ($this->assetCollection as $assetProject) {
            $out .= $assetProject->output();
        }

        return $out;
    }

    /**
     * convert an md string to html
     *
     * @param string $md markdown source
     *
     * @return string
     */
    public function mdToHtml($md)
    {
        $parser = new MarkdownParser();

        return $parser->transform($md);
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
