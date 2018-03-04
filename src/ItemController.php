<?php
namespace Kronofoto;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Interop\Container\ContainerInterface;

class ItemController 
{
    public function read(Request $request, Response $response, array $args) 
    {
        $response->getBody()->write("Item");
        return $response;
    }

    public function getItems(Request $request, Response $response, array $args)
    {
        echo 'items';
    }

    public function getDonorItems(Request $request, Response $response, array $args)
    {
        echo 'donor items';
    }

    public function getCollectionItems(Request $request, Response $response, array $args) 
    {
        echo 'collection items';
    }
}
