<?php
/**
 * User: matteo
 * Date: 09/10/12
 * Time: 0.15
 *
 * Just for fun...
 */

namespace Walrus\Project;

use Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Yaml\Yaml,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\Finder\Finder,
    Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Walrus\Configuration\MainConfiguration,
    Walrus\Theme\Theme,
    Walrus\Configuration\ThemeConfiguration,
    Walrus\Exception\MultipleThemeFoldersException,
    Walrus\Exception\ThemeFolderNotFound;
use Assetic\Filter\UglifyCssFilter,
    Assetic\Filter\UglifyJsFilter;

/**
 * Project class
 */
class Project
{
    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var string
     */
    private $configurationFile;

    /**
     * @var string
     */
    private $siteName;

    /**
     * @var Theme
     */
    private $theme;

    /**
     * @var string
     */
    private $themeName;

    /**
     * @var string
     */
    private $themeLocation;

    /**
     * @var string
     */
    private $uglifyCss;

    /**
     * @var string
     */
    private $uglifyJs;

    /**
     * constructor
     *
     * @param string $rootPath          root of the project
     * @param string $configurationFile name of the main configuration file
     */
    public function __construct($rootPath, Theme $theme, $configurationFile = 'walrus.yml')
    {
        $this->rootPath = $rootPath;
        $this->theme = $theme;
        $this->configurationFile = $configurationFile;
        $pc = $this->parseConfiguration($rootPath);
        $this->setSiteName($pc['site_name']);
        $this->setThemeName($pc['theme_name']);
        $this->setThemeLocation($pc['theme_location']);
        if ($pc['uglify_css']['enabled']) {
            $this->theme->getAssetCollection()->addFilter('css', new UglifyCssFilter($pc['uglify_css']['path']));
        }
        if ($pc['uglify_js']['enabled']) {
            $this->theme->getAssetCollection()->addFilter('js', new UglifyJsFilter($pc['uglify_js']['path']));
        }
    }

    /**
     * called from the dic
     * find the theme folder and build the theme
     */
    public function buildTheme()
    {
        if (null !== $this->getThemeLocation()) {
            $this->getTheme()->setThemePath($this->rootPath.'/'.$this->getThemeLocation());
            return;
        }
        $iterator = Finder::create()->files()->name('theme.yml')->in($this->getRootPath());
        if (0 === iterator_count($iterator)) {
            throw new ThemeFolderNotFound();
        }
        $themeFiles = array();
        foreach ($iterator as $file) {
            try {
                $config = Yaml::parse($this->rootPath.'/'.$file->getRelativePathname());
                $processor = new Processor();
                $conf = new ThemeConfiguration();
                $pc = $processor->processConfiguration($conf, $config);
                if ($this->getThemeName() === $pc['name']) {
                    $themeFiles[] = $file->getRelativePath();
                }
            } catch (InvalidConfigurationException $e) {
            }
        }
        if (1 < count($themeFiles)) {
            throw new MultipleThemeFoldersException();
        }
        $this->getTheme()->setThemePath($this->rootPath.'/'.$themeFiles[0]);
    }

    /**
     * parse main configuration file walrus.yml
     *
     * @param string $rootPath root path of the project
     *
     * @return array
     */
    private function parseConfiguration($rootPath)
    {
        $locator = new FileLocator($rootPath);
        try {
            $file = $locator->locate($this->configurationFile);
        } catch (\InvalidArgumentException $e) {
            $file = null;
        }
        if (file_exists($file)) {
            $config = Yaml::parse($file);
        } else {
            $config = array();
        }
        $processor = new Processor();
        $conf = new MainConfiguration();

        return $processor->processConfiguration($conf, $config);
    }

    /**
     * RootPath setter
     *
     * @param string $rootPath la variabile rootPath
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * RootPath getter
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * ConfigurationFile setter
     *
     * @param string $configurationFile la variabile configurationFile
     */
    public function setConfigurationFile($configurationFile)
    {
        $this->configurationFile = $configurationFile;
    }

    /**
     * ConfigurationFile getter
     *
     * @return string
     */
    public function getConfigurationFile()
    {
        return $this->configurationFile;
    }

    /**
     * SiteName setter
     *
     * @param string $siteName la variabile siteName
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
    }

    /**
     * SiteName getter
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * Theme setter
     *
     * @param \Walrus\Theme\Theme $theme la variabile theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * Theme getter
     *
     * @return \Walrus\Theme\Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * ThemeName setter
     *
     * @param string $themeName la variabile themeName
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;

    }

    /**
     * ThemeName getter
     *
     * @return string
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * ThemeLocation setter
     *
     * @param string $themeLocation la variabile themeLocation
     */
    public function setThemeLocation($themeLocation)
    {
        $this->themeLocation = $themeLocation;
    }

    /**
     * ThemeLocation getter
     *
     * @return string
     */
    public function getThemeLocation()
    {
        return $this->themeLocation;
    }

    /**
     * UglifyCss setter
     *
     * @param string $uglifyCss la variabile uglifyCss
     */
    public function setUglifyCss($uglifyCss)
    {
        $this->uglifyCss = $uglifyCss;
    }

    /**
     * UglifyCss getter
     *
     * @return string
     */
    public function getUglifyCss()
    {
        return $this->uglifyCss;
    }

    /**
     * UglifyJs setter
     *
     * @param string $uglifyJs la variabile uglifyJs
     */
    public function setUglifyJs($uglifyJs)
    {
        $this->uglifyJs = $uglifyJs;
    }

    /**
     * UglifyJs getter
     *
     * @return string
     */
    public function getUglifyJs()
    {
        return $this->uglifyJs;
    }
}
