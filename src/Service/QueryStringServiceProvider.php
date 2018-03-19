<?php
namespace Kronofoto\Service;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class QueryStringServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['qs'] = new QueryStringService($container);
    }
}

