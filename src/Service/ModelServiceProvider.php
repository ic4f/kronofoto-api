<?php
namespace Kronofoto\Service;

use Pimple\ServiceProviderInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Pimple\Container;
use Kronofoto\Models\CollectionModel;
use Kronofoto\Models\DonorModel;
use Kronofoto\Models\ItemModel;

class ModelServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['CollectionModel'] = new CollectionModel();
        $container['DonorModel'] = new DonorModel();
        $container['ItemModel'] = new ItemModel();
    }
}
