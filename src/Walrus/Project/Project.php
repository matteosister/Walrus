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
    Symfony\Component\Config\FileLocator;
use Walrus\Configuration\MainConfiguration;

/**
 * Project class
 */
class Project
{
    /**
     * @var string
     */
    private $configurationFile;

    /**
     * @var string
     */
    private $siteName;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $themeLocation;

    /**
     * constructor
     *
     * @param string $rootPath          root of the project
     * @param string $configurationFile name of the main configuration file
     */
    public function __construct($rootPath, $configurationFile = 'walrus.yml')
    {
        $this->configurationFile = $configurationFile;
        $pc = $this->parseConfiguration($rootPath);
        $this->setSiteName($pc['site_name']);
        $this->setTheme($pc['theme']);
        $this->setThemeLocation($pc['theme_location']);
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
     * @param string $theme la variabile theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * Theme getter
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
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


}
