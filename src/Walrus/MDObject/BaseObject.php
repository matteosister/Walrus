<?php
/**
 * User: matteo
 * Date: 07/09/12
 * Time: 15.36
 *
 * Just for fun...
 */

namespace Walrus\MDObject;

use Walrus\Exception\PageParseException,
    Walrus\Exception\MalformedMarkdownException;

use dflydev\markdown\MarkdownParser;

/**
 * Base object from .md parsing
 */
class BaseObject
{
    const SEPARATOR = "***";

    /**
     * @param string $md raw md content
     *
     * @throws \Walrus\Exception\PageParseException
     */
    protected function checkPage($md)
    {
        if (!preg_match('/^(.*)\*\*\*(\n?)(.*?)$/s', $md)) {
            throw new PageParseException();
        }
    }

    /**
     * create a metadata object from a raw md content
     *
     * @param string $md                md content
     * @param string $metadataClassName metadata class name
     *
     * @return array
     */
    protected function parseMetadata($md, $metadataClassName)
    {
        $sections = $this->splitContent($md);

        return new $metadataClassName($sections['metadata']);
    }

    /**
     * create a content object from a raw md content
     *
     * @param string $md               raw md content
     * @param string $contentClassName content class name
     *
     * @return array
     */
    protected function parseContent($md, $contentClassName)
    {
        $sections = $this->splitContent($md);

        return new $contentClassName($sections['content']);
    }

    /**
     * convert markdown to html
     *
     * @param string $md markdown raw content
     *
     * @return string
     */
    protected function toHtml($md)
    {
        $parser = new MarkdownParser();

        return $parser->transform($md);
    }

    /**
     * split a md file in two sections: metadata and content
     *
     * @param string $md raw md content
     *
     * @return array
     * @throws \Walrus\Exception\MalformedMarkdownException
     */
    private function splitContent($md)
    {
        $sections = preg_split(sprintf('/%s\n/', preg_quote(static::SEPARATOR)), $md);
        if (2 === count($sections)) {
            return array('metadata' => $sections[0], 'content' => $sections[1]);
        } else if (1 === count($sections)) {
            return array('metadata' => trim($sections[0], static::SEPARATOR), 'content' => '');
        } else {
            throw new MalformedMarkdownException();
        }
    }
}
