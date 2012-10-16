<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.35
 *
 * Just for fun...
 */

namespace Walrus\Asset\Project\Css;

use Walrus\Asset\ProjectInterface,
    Walrus\Asset\Project\AbstractProject,
    Walrus\Utilities\SlugifierTrait;
use CompassElephant\CompassProject;
use Symfony\Component\Finder\Finder,
    Symfony\Component\Filesystem\Filesystem;
use Assetic\Asset\GlobAsset,
    Assetic\Asset\AssetCollection;

/**
 * Compass project
 */
class Compass extends AbstractProject implements ProjectInterface
{
    use SlugifierTrait;

    /**
     * @var CompassProject
     */
    private $project;

    /**
     * @var string
     */
    private $name = 'compass';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * class constructor
     *
     * @param CompassProject $project project
     * @param null|string    $name    name
     */
    public function __construct(CompassProject $project, $name = null)
    {
        $this->project = $project;
        if (null !== $name) {
            $this->name = $name;
        }
        $this->filesystem = new Filesystem();
    }

    /**
     * compile a project
     *
     * @return bool
     */
    public function compile()
    {
        $this->project->compile(true);
    }

    /**
     * publish the generate files to the final destination
     *
     * @param null|string                     $to     folder to publish to
     * @param \Assetic\Filter\FilterInterface $filter assetic filter
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
     * @param null|\Assetic\Filter\FilterInterface $filter FilterInterface
     *
     * @return string
     */
    public function getStream($filter = null)
    {
        $iterator = Finder::create()->files()->name('*.css')->in($this->getOutputFolder());
        $assetCollection = new AssetCollection();
        foreach ($iterator as $file) {
            $assetCollection->add(new GlobAsset($file->getPathName()));
        }
        if (null !== $filter) {
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
        $iterator = Finder::create()->files()->name('*.css')->in($this->getOutputFolder());
        $output = '';
        foreach ($iterator as $file) {
            $output .= sprintf('<link rel="stylesheet" type="text/css" href="/%s/%s">', $this->getProjectType(), $this->getOutputFilename());
        }

        return $output;
    }

    /**
     * generates the output filename
     *
     * @return string
     */
    public function getOutputFilename()
    {
        return $this->slugify($this->name).'.css';
    }

    /**
     * output folder of compass compile
     *
     * @return string
     */
    private function getOutputFolder()
    {
        return $this->project->getProjectPath().DIRECTORY_SEPARATOR.'stylesheets';
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
     * get project type
     *
     * @return string
     */
    public function getProjectType()
    {
        return static::TYPE_CSS;
    }
}
