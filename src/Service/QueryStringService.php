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
        $t = $this->container['settings']['test42'];
        return $t;
    }

    //TODO this is where all the query string processing goes.
}
