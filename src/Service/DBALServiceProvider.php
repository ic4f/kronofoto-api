<?php
namespace Kronofoto\Service;

use Pimple\ServiceProviderInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Pimple\Container;

class DBALServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $params = array(
            'driver' => 'pdo_mysql',
            'driverOptions' => array( 
                //to stop ints/floats from being converted to strings
                \PDO::ATTR_EMULATE_PREPARES => false
            )
        );

        $appDbParams = $container['settings']['db'];

        $params = array_merge($params, $appDbParams);

        $container['db'] = DriverManager::getConnection($params, new Configuration());
    }

}
