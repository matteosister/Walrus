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
    Walrus\Exception\UrlNotFoundException;
use dflydev\markdown\IMarkdownParser;

class MdContentExtension extends \Twig_Extension
{
    /**
     * @var \Walrus\Collection\PageCollection
     */
    private $pageCollection;

    /**
     * class constructor
     *
     * @param \Walrus\Collection\PageCollection $pageCollection
     */
    public function __construct(PageCollection $pageCollection)
    {
        $this->pageCollection = $pageCollection;
    }

    public function getFunctions()
    {
        return array(
            'url_for' => new \Twig_Function_Method($this, 'urlFor', array('is_safe' => array('all'))),
            'link_to' => new \Twig_Function_Method($this, 'linkTo', array('is_safe' => array('all')))
        );
    }

    public function urlFor($slug)
    {
        $pages = array_filter($this->pageCollection->toArray(), function(Page $page) use ($slug) {
            return $page->getMetadata()->getUrl() == $slug;
        });
        if (count($pages) == 0) {
            throw new UrlNotFoundException();
        }
        sort($pages);
        $page = $pages[0];
        if ($page->getMetadata()->getHomepage()) {
            return '/';
        } else {
            return '/'.$page->getMetadata()->getUrl().'.html';
        }
    }

    public function linkTo($slug, $label = null, $title = null)
    {
        $url = $this->urlFor($slug);
        if (null == $label && null == $title) {
            return sprintf('[%s](%s)', $url, $url);
        } else {
            if (null !== $label && null !== $title) {
                return sprintf('[%s](%s "%s")', $label, $url, $title);
            } else {
                if (null !== $label) {
                    return sprintf('[%s](%s)', $label, $url);
                } else {
                    return sprintf('[%s](%s "%s")', $url, $url, $title);
                }
            }
        }
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
