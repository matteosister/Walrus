<?php
/**
 * User: matteo
 * Date: 24/09/12
 * Time: 22.30
 *
 * Just for fun...
 */

namespace Walrus\Twig\Extension;

use Walrus\MDObject\Page\Page,
    Walrus\Twig\Extension\WalrusExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Walrus\Exception\UrlNotFoundException;

/**
 * extension for md content
 */
class MdContentExtension extends WalrusExtension
{
/*    public function linkTo($slug, $label = null, $title = null)
    {
        try {
            $url = $this->urlFor($slug);
        } catch (UrlNotFoundException $e) {
            $url = $slug;
        }
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
    }*/

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'md_content_extension';
    }
}
