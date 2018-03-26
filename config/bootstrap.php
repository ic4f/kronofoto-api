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



//TODO: change this: URL needs to be moved to config settings; should be different for production and dev.
//Also, review security considerations; check allowed headers.
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET');
});



return $app;
