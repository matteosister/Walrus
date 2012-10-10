<?php
/**
 * User: matteo
 * Date: 08/10/12
 * Time: 21.55
 *
 * Just for fun...
 */

namespace Walrus\Theme;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Config\Definition\Processor,
    Walrus\Configuration\ThemeConfiguration,
    Symfony\Component\Yaml\Yaml;
use Walrus\Asset\AssetCollection,
    Walrus\Asset\Project\Css\CssFolder,
    Walrus\Asset\Project\Css\Compass,
    Walrus\Asset\Project\Css\Less,
    Walrus\Asset\Project\Js\JsFolder,
    Walrus\Asset\Project\Js\JsFile;
use CompassElephant\CompassProject;
use LessElephant\LessProject;

/**
 * Theme class
 */
class Theme
{
    /**
     * @var string
     */
    private $themePath;
    /**
     * @var \Walrus\Asset\AssetCollection
     */
    private $assetCollection;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $images;

    /**
     * @var bool
     */
    private $compressAssets;

    public function __construct($themePath, AssetCollection $assetCollection)
    {
        $this->themePath = realpath($themePath);
        $this->assetCollection = $assetCollection;
        $config = Yaml::parse($themePath.'/theme.yml');
        $processor = new Processor();
        $conf = new ThemeConfiguration();
        $pc = $processor->processConfiguration($conf, $config);
        $this->name = $pc['name'];
        $this->images = $this->themePath.'/'.$pc['images'];
        $this->compressAssets = $pc['compress_assets'];
        $this->assetCollection->setGroupAssets($pc['group_assets']);
        foreach($pc['assets'] as $assetsConfiguration) {
            switch($assetsConfiguration['type']) {
                case 'compass':
                    $this->compassConfiguration($assetsConfiguration);
                    break;
                case 'less':
                    $this->lessConfiguration($assetsConfiguration);
                    break;
                case 'css_source':
                    $this->cssFolderConfiguration($assetsConfiguration);
                    break;
                case 'js_source':
                    $this->jsFolderConfiguration($assetsConfiguration);
                    break;
                case 'js_file':
                    $this->jsFileConfiguration($assetsConfiguration);
                    break;
            }
        }
    }

    private function compassConfiguration($conf)
    {
        $sourceFolder = $this->themePath.'/'.$conf['source_folder'];
        if (is_dir($sourceFolder)) {
            $compassProject = new CompassProject($sourceFolder);
            $compass = new Compass($compassProject, $conf['name']);
            $compass->setCompress($this->compressAssets);
            $this->assetCollection->addProject($compass);
        } else {
            throw new \RuntimeException(sprintf('the folder %s do not exists, the compass project couldn\'t be initialized', $sourceFolder));
        }
    }

    private function lessConfiguration($conf)
    {
        $sourceFile = $this->themePath.'/'.$conf['source_file'];
        $filename = sys_get_temp_dir().'/less_'.sha1(uniqid()).'.css';
        $destFile = $filename;
        if (is_file($sourceFile)) {
            $pathParts = pathinfo($sourceFile);
            $dir = $pathParts['dirname'];
            $name = $pathParts['basename'];
            $lessProject = new LessProject($dir, $name, $destFile);
            $less = new Less($lessProject, $conf['name']);
            $less->setCompress($this->compressAssets);
            $this->assetCollection->addProject($less);
        } else {
            throw new \RuntimeException(sprintf('the file %s do not exists, the less project could not be initialized', $sourceFile));
        }
    }

    private function cssFolderConfiguration($conf)
    {
        // TODO: validate folder paths
        $fileFolder = $this->themePath.'/'.$conf['source_folder'];
        $cssFolder = new CssFolder($fileFolder, $conf['name']);
        $cssFolder->setCompress($this->compressAssets);
        $this->assetCollection->addProject($cssFolder);
    }

    private function jsFolderConfiguration($conf)
    {
        // TODO: validate folder paths
        $fileFolder = $this->themePath.'/'.$conf['source_folder'];
        $jsFolder = new JsFolder($fileFolder, $conf['name']);
        $jsFolder->setCompress($this->compressAssets);
        $this->assetCollection->addProject($jsFolder);
    }

    private function jsFileConfiguration($conf)
    {
        $file = $this->themePath.'/'.$conf['source_file'];
        $jsFile = new JsFile($file, $conf['name']);
        $jsFile->setCompress($this->compressAssets);
        $this->assetCollection->addProject($jsFile);
    }

    /**
     * AssetProjects setter
     *
     * @param \Walrus\Asset\AssetCollection $assetProjects la variabile assetProjects
     */
    public function setAssetProjects($assetProjects)
    {
        $this->assetCollection = $assetProjects;
    }

    /**
     * AssetProjects getter
     *
     * @return \Walrus\Asset\AssetCollection
     */
    public function getAssetProjects()
    {
        return $this->assetCollection;
    }

    /**
     * Images setter
     *
     * @param string $images la variabile images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * Images getter
     *
     * @return string
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Name setter
     *
     * @param string $name la variabile name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Name getter
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * CompressAssets setter
     *
     * @param boolean $compressAssets la variabile compressAssets
     */
    public function setCompressAssets($compressAssets)
    {
        $this->compressAssets = $compressAssets;
    }

    /**
     * CompressAssets getter
     *
     * @return boolean
     */
    public function getCompressAssets()
    {
        return $this->compressAssets;
    }
}
