<?php
/**
 * User: matteo
 * Date: 10/10/12
 * Time: 21.58
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project\Js;

use Walrus\Asset\Project\AbstractProject,
    Walrus\Asset\ProjectInterface,
    Walrus\Utilities\SlugifierTrait,
    Walrus\Exception\FileNotFoundException;
use Assetic\Asset\GlobAsset;

/**
 * JsFile project
 */
class JsFile extends AbstractProject implements ProjectInterface
{
    use SlugifierTrait;

    /**
     * @var null|string
     */
    private $name = 'js file';

    /**
     * @var string
     */
    private $file;

    /**
     * Class constructor
     *
     * @param string $folder folder
     * @param null   $name   project name
     */
    public function __construct($file, $name = null)
    {
        if (!is_file($file)) {
            throw new FileNotFoundException();
        }
        $this->file = $file;
        if (null !== $name) {
            $this->name = $name;
        }
    }

    /**
     * @return string
     */
    public function getProjectType()
    {
        return static::TYPE_JS;
    }

    /**
     * compile a project
     *
     * @return bool
     */
    public function compile()
    {
    }

    /**
     * publish the generated files to the final destination
     *
     * @param null $to     publish to
     * @param null $filter FilterInterface
     *
     * @return null
     */
    public function publish($to = null, $filter = null)
    {
        file_put_contents($to.'/'.$this->getOutputFilename(), $this->getStream($filter));
    }

    /**
     * get the output stream
     *
     * @param null $filter FilterInterface
     *
     * @return string
     */
    public function getStream($filter = null)
    {
        $asset = new GlobAsset(realpath($this->file));
        if (null !== $filter && $this->compress) {
            $asset->ensureFilter($filter);
        }
        return $asset->dump();
    }

    /**
     * get the output for the header of the page
     *
     * @return string
     */
    public function output()
    {
        return sprintf('<script type="text/javascript" src="/%s/%s"></script>', $this->getProjectType(), $this->getOutputFilename());
    }

    /**
     * @return string
     */
    public function getOutputFilename()
    {
        return $this->slugify($this->name).'.js';
    }

    /**
     * project name getter
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
