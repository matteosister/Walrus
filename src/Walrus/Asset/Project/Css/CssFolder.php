<?php
/**
 * User: matteo
 * Date: 18/09/12
 * Time: 23.17
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project\Css;

use Walrus\Asset\ProjectInterface,
    Walrus\Asset\Project\AbstractProject,
    Walrus\Utilities\SlugifierTrait;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Assetic\Asset\GlobAsset,
    Assetic\Asset\AssetCollection;

/**
 * CssFolder project
 */
class CssFolder extends AbstractProject implements ProjectInterface
{
    use SlugifierTrait;

    /**
     * @var string
     */
    private $folder;

    /**
     * @var string
     */
    private $name = 'css folder';

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
        $iterator = Finder::create()->files()->name('*.css')->in($this->folder);
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
        $iterator = Finder::create()->files()->name('*.css')->in($this->folder);
        $output = '';
        foreach ($iterator as $file) {
            $output .= sprintf('<link rel="stylesheet" type="text/css" href="/%s/%s" media="screen, projection">', $this->getProjectType(), $this->getOutputFilename());
        }

        return $output;
    }

    /**
     * output folder of compass compile
     *
     * @return string
     */
    public function getOutputFilename()
    {
        return $this->slugify($this->name).'.css';
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
        return static::TYPE_CSS;
    }
}
