<?php
/**
 * User: matteo
 * Date: 28/08/12
 * Time: 22.33
 *
 * Just for fun...
 */

namespace Walrus\Utilities;

/**
 * Utilities class
 */
class Utilities
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

    /**
     * generate a slug, and check if it's unique, if not generate another...
     *
     * @param array  $existing    slug already generated
     * @param string $slug        string to slugify
     * @param string $replacement replacement
     *
     * @return mixed|string
     */
    public function getUniqueSlug($existing, $slug, $replacement = '-')
    {
        $num = 1;
        $generatedSlug = $this->slugify($slug, $replacement);
        while (in_array($generatedSlug, $existing)) {
            $generatedSlug = $this->slugify($slug.' '.$num, $replacement);
            $num++;
        }

        return $generatedSlug;
    }

    /**
     * get the formatted date
     *
     * @param string $format
     *
     * @return string
     */
    public function getDateFormatted($format = 'Y-m-d_H:i:s')
    {
        return date($format);
    }
}
