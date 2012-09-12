<?php
/**
 * User: matteo
 * Date: 29/08/12
 * Time: 12.39
 *
 * Just for fun...
 */

namespace Walrus\DI;

use Walrus\Exception\MissingConfigurationParameter;

/**
 * DI configuration class
 */
class Configuration
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->configuration = array();
    }

    /**
     * @param string $name  parameter name
     * @param mixed  $value value
     */
    public function set($name, $value)
    {
        $this->configuration[$name] = $value;
    }

    /**
     * @param string $name parameter name
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function get($name)
    {
        if (isset($this->configuration[$name])) {
            return $this->configuration[$name];
        } else {
            throw new MissingConfigurationParameter(sprintf('there is no configuration parameter named %s', $name));
        }
    }

    /**
     * magic method for properties
     *
     * @param string $name name getter
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}
