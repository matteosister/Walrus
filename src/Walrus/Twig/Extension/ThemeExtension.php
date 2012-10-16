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
    Walrus\Collection\PageCollection,
    Walrus\Twig\Extension\WalrusExtension;
use Symfony\Component\Finder\Finder,
    Symfony\Component\DependencyInjection\ContainerInterface;
use dflydev\markdown\MarkdownParser;

/**
 * theme extension
 */
class ThemeExtension extends WalrusExtension
{
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
        return array_merge(parent::getFunctions(), array(
            'assets' => new \Twig_Function_Method($this, 'assets', array('is_safe' => array('all')))
        ));
    }

    /**
     * globals
     *
     * @return array
     */
    public function getGlobals()
    {
        return array_merge(parent::getGlobals(), array(
            'pages' => $this->container->get('walrus.collection.page')
        ));
    }


    /**
     * stylesheets
     *
     * @return string
     */
    public function assets()
    {
        return $this->container->get('walrus.theme')->getAssetProjects()->output();
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
