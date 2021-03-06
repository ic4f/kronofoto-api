<?php
namespace Kronofoto;

use Kronofoto\Service\DBALServiceProvider;
use Kronofoto\Service\ModelServiceProvider;

defined('ROOT_PATH') || define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

//get settings and instantiate app
$settings = require ROOT_PATH . '/config/settings.php';
$app = new \Slim\App($settings);

//add dependencies
$container = $app->getContainer();
$container->register(new DBALServiceProvider());
$container->register(new ModelServiceProvider());


//get routes
require ROOT_PATH . '/config/routes.php';

return $app;
