<?php
namespace Kronofoto\Test\Unit\Controllers;


use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Query\QueryBuilder;

use Kronofoto\Controllers\PageController;

abstract class ControllerTest extends \Codeception\Test\Unit
{
    private $container; 
    private $app;

    protected function _before()
    {
        $this->app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $this->app->getContainer();
        $this->container['db'] = $this->getDbStub(); // override db provider
    }

    protected function getResponse($method, $uri, $qs='')
    {
        $env = Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $uri,
            'QUERY_STRING' => $qs,
            'CONTENT_TYPE' => 'application/json;charset=utf8'
        ]);

        $request = Request::createFromEnvironment($env);
        return $this->app->__invoke($request, new Response());
    }

    private function getDbStub()
    {
        $statementStub = $this->make(Statement::class, [
            'fetch' => true,
            'fetchColumn' => true,
            'fetchAll' => true,
        ]);
        $queryBuilderStub = $this->make(QueryBuilder::class, [
            'execute' => $statementStub 
        ]);
        $dbStub = $this->make(Connection::class, [
            'createQueryBuilder' => $queryBuilderStub
        ]);
        return $dbStub;
    }
}
