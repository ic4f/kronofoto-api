<?php
namespace Kronofoto;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Interop\Container\ContainerInterface;

class ItemController 
{
    protected $container;
    private $test42;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->test42 = 'abra';
    }
    //this must return an http json response containing an item...
    //
    //
    //
    public function read(Request $request, Response $response, array $args) 
    {
        //args  must contain a valid identifier (accession number?)
        //

        //get accession number
        $id = $args['id'];

        //get config vals

        $db = $this->container->db1;
        $sql = "select * from archive_photo where id = 2";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        $result = print_r($result);

        //call db
       // $item = $db->getItem($id);

        //arrange json
        //$result = json_encode($item);
        
        //send json as the http response

        



        $response->getBody()->write($result);
        return $response;
    }

    public function getItems(Request $request, Response $response, array $args)
    {
        //args may be empty, or may contain query string
        //if args not enmpty, must process query string first
        //include additional info about recordset into json.

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
