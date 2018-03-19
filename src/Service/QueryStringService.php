<?php
namespace Kronofoto\Service;

use Pimple\Container;

class QueryStringService
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function test5() 
    {
        return 5;
    }

    //TODO this is where all the query string processing goes.
}
