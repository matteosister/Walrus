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
    Symfony\Component\Yaml\Yaml;
use Walrus\Configuration\MainConfiguration;

/**
 * Project class
 */
class Project
{
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
     * @param string $rootPath root of the project
     */
    public function __construct($rootPath)
    {
        $pc = $this->parseConfiguration($rootPath.'/walrus.yml');
        $this->siteName = $pc['site_name'];
        $this->theme = $pc['theme'];
        $this->themeLocation = $pc['theme_location'];
    }

    private function parseConfiguration($file)
    {
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
