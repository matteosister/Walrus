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

    /**
     * globals
     *
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'project' => $this->container->get('walrus.project')
        );
    }

    public function urlFor($slug)
    {
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

    /**
     * @return \Walrus\Collection\PageCollection
     */
    protected function getPageCollection()
    {
        return $this->container->get('walrus.collection.page');
    }
}
