<?php
/**
 * User: matteo
 * Date: 18/09/12
 * Time: 23.17
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project\Js;

use Walrus\Asset\ProjectInterface,
    Walrus\Asset\Project\AbstractProject,
    Walrus\Utilities\SlugifierTrait;
use Symfony\Component\Finder\Finder;
use Assetic\Asset\AssetCollection,
    Assetic\Asset\GlobAsset;

/**
 * CssFolder project
 */
class JsFolder extends AbstractProject implements ProjectInterface
{
    use SlugifierTrait;

    /**
     * @var string
     */
    private $folder;

    /**
     * @var string
     */
    private $name = 'js folder';

    /**
     * Class constructor
     *
     * @param string $folder folder
     * @param null   $name   project name
     */
    public function __construct($folder, $name = null)
    {
        $this->folder = $folder;
        if (null !== $name) {
            $this->name = $name;
        }
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
     * publish the generate files to the final destination
     *
     * @param null $to     publish to
     * @param null $filter filter
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
        $iterator = Finder::create()->files()->name('*.js')->in($this->folder)->depth('== 0');
        $assetCollection = new AssetCollection();
        foreach ($iterator as $file) {
            $assetCollection->add(new GlobAsset(realpath($file->getPathName())));
        }
        if (null !== $filter && $this->compress) {
            $assetCollection->ensureFilter($filter);
        }

        return $assetCollection->dump();
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

    /**
     * @return string
     */
    public function getProjectType()
    {
        return static::TYPE_JS;
    }
}
