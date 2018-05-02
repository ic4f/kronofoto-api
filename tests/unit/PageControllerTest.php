<?php
namespace Kronofoto\Test;

use Kronofoto\Controllers\PageController;

class PageControllerTest extends \Codeception\Test\Unit
{
    protected $container; 

    protected function _before()
    {
        $app = require dirname(dirname(__DIR__)) . '/config/bootstrap.php';
        $this->container = $app->getContainer();
    }

    public function testFoo()
    {
        //create an api with a mock data provider.
       //make a request 
        //
        //
        //

    }


}
