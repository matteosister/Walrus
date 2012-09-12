<?php
/**
 * User: matteo
 * Date: 10/09/12
 * Time: 17.25
 *
 * Just for fun...
 */

namespace Walrus\MDObject;

/**
 * Base Class for metadata
 */
abstract class BaseMetadata
{
    /**
     * parse a metadata line
     *
     * @param string $line parse a metadata line
     */
    protected function parseLine($line)
    {
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties() as $prop) {
            $propName = $prop->getName();
            $matches = array();
            $regexp = sprintf('/^%s\:\s*(.*)$/', $propName);
            if (preg_match($regexp, $line, $matches)) {
                $this->$propName = $matches[1];
            }
        }
    }

    /**
     * parse lines of the metadata looking for class props
     *
     * @param array $lines lines of metadata
     */
    protected function parseLines($lines)
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties();
        foreach ($lines as $line) {
            $this->parseLine($line);
        }
    }
}
