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
    Walrus\Exception\UrlNotFoundException,
    Walrus\Twig\Extension\WalrusExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MdContentExtension extends WalrusExtension
{
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
