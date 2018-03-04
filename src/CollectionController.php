<?php
namespace Kronofoto;

//use Interop\Container\ContainerInterface;

class CollectionController 
{
    public function read(Request $request, Response $response, array $args) 
    {
        echo 'collection';
    }

    public function getCollections(Request $request, Response $response, array $args) 
    {
        echo 'collecitons';
    }

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
        echo 'donor collecitons';
    }
}
