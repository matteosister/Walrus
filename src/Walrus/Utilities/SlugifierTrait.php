<?php
/**
 * User: matteo
 * Date: 30/09/12
 * Time: 0.16
 *
 * Just for fun...
 */

namespace Walrus\Utilities;

/**
 * slugify function
 */
trait SlugifierTrait
{
    /**
     * Modifies a string to remove all non ASCII characters and spaces.
     *
     * @param string $slug        the original string
     * @param string $replacement the standard replacement
     *
     * @return mixed|string
     */
    public function slugify($slug, $replacement = '-')
    {
        // transliterate
        if (function_exists('iconv')) {
            $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        }

        // lowercase
        if (function_exists('mb_strtolower')) {
            $slug = mb_strtolower($slug);
        } else {
            $slug = strtolower($slug);
        }

        // remove accents resulting from OSX's iconv
        $slug = str_replace(array('\'', '`', '^'), '', $slug);

        // replace non letter or digits with separator
        $slug = preg_replace('/\W+/', $replacement, $slug);

        // trim
        $slug = trim($slug, $replacement);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }
}