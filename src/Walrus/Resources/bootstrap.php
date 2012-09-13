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

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\YamlFileLoader,
    Symfony\Component\Config\FileLocator;

use Symfony\Component\Console\Application;

$container = new ContainerBuilder();
$container->setParameter("ROOT_PATH", realpath(__DIR__.'/../../../../'));
$container->setParameter("PUBLIC_PATH", realpath(__DIR__.'/../../../../public'));

$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/config'));
$loader->load('templating.yml');
$loader->load('commands.yml');
$loader->load('utilities.yml');
$loader->load('configuration.yml');
$loader->load('less.yml');

$application = new Application();
$application->add($container->get('create_post.command'));
$application->add($container->get('create_page.command'));
$application->add($container->get('generate_site.command'));
