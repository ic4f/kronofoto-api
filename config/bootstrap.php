<?php
namespace Kronofoto;

defined('ROOT_PATH') || define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';

//get settings and instantiate app
$settings = require ROOT_PATH . '/config/settings.php';
$app = new \Slim\App($settings);





////TODO change this to DBAL
//add dependencies
//$container = $app->getContainer();
//$container['db2'] = function($c) {
//    $dbal = new DbalService();
//    return 'test';
//};
//
//$container['db1'] = function ($c) {
//    $db = $c['settings']['db'];
//    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
//        $db['user'], $db['pass']);
//    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    return $pdo;
//};




//get routes
require ROOT_PATH . '/config/routes.php';

return $app;
