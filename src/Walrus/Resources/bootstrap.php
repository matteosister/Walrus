<?php
/**
 * Created by JetBrains PhpStorm.
 * User: matteo
 * Date: 29/08/12
 * Time: 16.40
 *
 * Just for fun...
 */

require_once __DIR__.'/../../../../vendor/autoload.php';
require_once __DIR__.'/../../../../vendor/twig/twig/lib/Twig/Autoloader.php';

Twig_Autoloader::register();

$walrus = new \Walrus\Walrus(__DIR__.'/../../../../');