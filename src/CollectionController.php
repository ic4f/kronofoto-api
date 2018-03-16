<?php
namespace Kronofoto;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class CollectionController 
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
 
    public function read(Request $request, Response $response, array $args) 
    {
    }

    public function getCollections(Request $request, Response $response, array $args) 
    {
        $conn = $this->container->db;

        $sql = 'select * from archive_collection';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $response->withJson($result);
    }

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
    }
}
