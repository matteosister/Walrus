<?php
/**
 * User: matteo
 * Date: 14/09/12
 * Time: 14.34
 *
 * Just for fun...
 */

namespace Walrus\Asset;

use Walrus\Asset\ProjectInterface,
    Walrus\Command\OutputWriterTrait,
    Walrus\Asset\Project\AbstractProject;
use Assetic\Filter\FilterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Walrus\Theme\Theme;

/**
 * Assets Collection
 */
class AssetCollection implements \Countable, \ArrayAccess, \Iterator
{
    use OutputWriterTrait;

    /**
     * @var array
     */
    private $projects;

    /**
     * @var int
     */
    private $position;

    /**
     * @var \Assetic\Filter\FilterInterface
     */
    private $cssFilter;

    /**
     * @var \Assetic\Filter\FilterInterface
     */
    private $jsFilter;

    /**
     * @var bool
     */
    private $groupAssets;

    /**
     * @var bool
     */
    private $forceAssetCompression;

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->forceAssetCompression = false;
        $this->projects = array();
    }

    /**
     * add a yuiCssCompressor for compiling assets with assetic
     *
     * @param string                          $type   css or js
     * @param \Assetic\Filter\FilterInterface $filter FilterIinterface instance
     *
     * @throws \RuntimeException
     * @return void
     * @internal param \Assetic\Filter\Yui\CssCompressorFilter $yuiCssCompressor
     */
    public function addFilter($type, FilterInterface $filter)
    {
        switch ($type) {
            case 'css':
                $this->cssFilter = $filter;

                return;
                break;
            case 'js':
                $this->jsFilter = $filter;

                return;
                break;
        }
        throw new \RuntimeException(sprintf('There is no %s filter type in asset collection', $type));
    }

    /**
     * add a project to the internal collection
     *
     * @param ProjectInterface $project a project
     */
    public function addProject(ProjectInterface $project)
    {
        $this->projects[] = $project;
    }

    /**
     * compile
     */

    /**
     * compile assets
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     * @param string                                            $to     folder
     */
    public function compile(OutputInterface $output, $to)
    {
        if ($this->groupAssets) {
            $this->compileGrouped($output, $to);

            return;
        }
        foreach ($this->projects as $project) {
            if ($this->getForceAssetCompression()) {
                $project->setCompress(true);
            }
            $output->writeln($this->getLine('compiling', sprintf('<comment>%s</comment> project', $project->getName())));
            $project->compile();
            $output->writeln($this->getLine('publishing', sprintf('<comment>%s</comment> project', $project->getName())));
            if (AbstractProject::TYPE_CSS === $project->getProjectType()) {
                $project->publish($to.'/'.$project->getProjectType(), $this->cssFilter);
            }
            if (AbstractProject::TYPE_JS === $project->getProjectType()) {
                $project->publish($to.'/'.$project->getProjectType(), $this->jsFilter);
            }
        }
    }

    /**
     * compile assets grouped by type
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output OutputInterface
     * @param string                                            $to     folder
     *
     * @return mixed
     */
    private function compileGrouped(OutputInterface $output, $to)
    {
        $css = '';
        $js = '';
        $output->writeln($this->getLine('loading', sprintf('stylesheets (%s)', implode(', ', array_map(function($project) {

            return '<comment>'.$project->getName().'</comment>';
        }, $this->getStylesheetProjects())))));
        foreach ($this->getStylesheetProjects() as $project) {
            $output->writeln($this->getLine('compiling', sprintf('<comment>%s</comment>', $project->getName())));
            if ($this->getForceAssetCompression()) {
                $project->setCompress(true);
            }
            $project->compile();
            $css .= sprintf("/* %s*/\n%s\n", $project->getName(), $project->getStream($this->cssFilter));
        }
        $output->writeln($this->getLine('loading', sprintf('javascripts (%s)', implode(', ', array_map(function($project) {
            return '<comment>'.$project->getName().'</comment>';
        }, $this->getJavascriptProjects())))));
        foreach ($this->getJavascriptProjects() as $project) {
            $output->writeln($this->getLine('compiling', sprintf('<comment>%s</comment>', $project->getName())));
            if ($this->getForceAssetCompression()) {
                $project->setCompress(true);
            }
            $project->compile();
            $js .= sprintf("/* %s */\n%s\n", $project->getName(), $project->getStream($this->jsFilter));
        }
        file_put_contents($to.'/stylesheets/all.css', $css);
        file_put_contents($to.'/javascripts/all.js', $js);
    }

    /**
     * @return string
     */
    public function output()
    {
        $out = '';
        if ($this->groupAssets) {
            $out .= '<link rel="stylesheet" type="text/css" href="/stylesheets/all.css">'."\n";
            $out .= '<script type="text/javascript" src="/javascripts/all.js"></script>'."\n";
        } else {
            foreach ($this->projects as $project) {
                $out .= $project->output();
            }
        }

        return $out;
    }

    /**
     * get stylesheet projects
     *
     * @return array
     */
    public function getStylesheetProjects()
    {
        return array_filter($this->projects, function(AbstractProject $project) {
            return $project->getProjectType() == AbstractProject::TYPE_CSS;
        });
    }

    /**
     * get javascript projects
     *
     * @return array
     */
    public function getJavascriptProjects()
    {
        return array_filter($this->projects, function(AbstractProject $project) {
            return $project->getProjectType() == AbstractProject::TYPE_JS;
        });
    }

    /**
     * GroupAssets setter
     *
     * @param boolean $groupAssets la variabile groupAssets
     */
    public function setGroupAssets($groupAssets)
    {
        $this->groupAssets = $groupAssets;
    }

    /**
     * GroupAssets getter
     *
     * @return boolean
     */
    public function getGroupAssets()
    {
        return $this->groupAssets;
    }

    /**
     * ForceAssetCompression setter
     *
     * @param boolean $forceAssetCompression la variabile forceAssetCompression
     */
    public function setForceAssetCompression($forceAssetCompression)
    {
        $this->forceAssetCompression = $forceAssetCompression;
    }

    /**
     * ForceAssetCompression getter
     *
     * @return boolean
     */
    public function getForceAssetCompression()
    {
        return $this->forceAssetCompression;
    }

    /**
     * CssFilter setter
     *
     * @param \Assetic\Filter\FilterInterface $cssFilter la variabile cssFilter
     */
    public function setCssFilter($cssFilter)
    {
        $this->cssFilter = $cssFilter;
    }

    /**
     * CssFilter getter
     *
     * @return \Assetic\Filter\FilterInterface
     */
    public function getCssFilter()
    {
        return $this->cssFilter;
    }

    /**
     * JsFilter setter
     *
     * @param \Assetic\Filter\FilterInterface $jsFilter la variabile jsFilter
     */
    public function setJsFilter($jsFilter)
    {
        $this->jsFilter = $jsFilter;
    }

    /**
     * JsFilter getter
     *
     * @return \Assetic\Filter\FilterInterface
     */
    public function getJsFilter()
    {
        return $this->jsFilter;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *
     *
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->projects);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     *       The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->projects[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->projects[$offset]) ? $this->projects[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->objects[] = $value;
        } else {
            $this->projects[$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->projects[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->projects[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->projects[$this->position]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }
}
