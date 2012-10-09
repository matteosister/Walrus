<?php
/**
 * User: matteo
 * Date: 28/08/12
 * Time: 22.33
 *
 * Just for fun...
 */

namespace Walrus\Utilities;

use Walrus\Utilities\SlugifierTrait;

/**
 * Utilities class
 */
class Utilities
{
    use SlugifierTrait;

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
    public function getDateFormatted($format = 'Y-m-d_H:i')
    {
        return (string) date($format);
    }
}
