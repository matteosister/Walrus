<?php
/**
 * User: matteo
 * Date: 01/10/12
 * Time: 0.26
 *
 * Just for fun...
 */

namespace Walrus\Twig\Extension;

use Walrus\Exception\UrlNotFoundException,
    Walrus\MDObject\Page\Page;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * generic extension
 */
abstract class WalrusExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * class constructor
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'url_for' => new \Twig_Function_Method($this, 'urlFor', array('is_safe' => array('all'))),
            'link_to' => new \Twig_Function_Method($this, 'linkTo', array('is_safe' => array('all')))
        );
    }

    public function urlFor(Page $page)
    {
        $slug = $page->getMetadata()->getUrl();
        $pages = array_filter($this->getPageCollection()->toArray(), function(Page $page) use ($slug) {
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

    public function linkTo(Page $page, $label = null, $title = null)
    {
        $url = $this->urlFor($page);
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
     * @return \Walrus\Collection\PageCollection
     */
    protected function getPageCollection()
    {
        return $this->container->get('walrus.collection.page');
    }
}
