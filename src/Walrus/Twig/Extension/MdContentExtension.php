<?php
/**
 * User: matteo
 * Date: 24/09/12
 * Time: 22.30
 *
 * Just for fun...
 */

namespace Walrus\Twig\Extension;

use Walrus\Collection\PageCollection,
    Walrus\MDObject\Page\Page,
    Walrus\Exception\UrlNotFoundException,
    Walrus\Twig\Extension\WalrusExtension;

class MdContentExtension extends WalrusExtension
{
    /**
     * class constructor
     *
     * @param \Walrus\Collection\PageCollection $pageCollection
     */
    public function __construct(PageCollection $pageCollection)
    {
        $this->pageCollection = $pageCollection;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'md_content_extension';
    }
}
