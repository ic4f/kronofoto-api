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

        $qb = $conn->createQueryBuilder();

        //now check for, then process the query string
        //
        //$sort = $request->getQueryParam('sort');
        $qp = $request->getQueryParams();

        if (array_key_exists('sort', $qp)) {
            $sort = $qp['sort'];

            $order = 'ASC';
            //is there ordering specified?
            if ($sort[0] == '+' || $sort[0] == '-') {
                if ($sort[0] == '-') {
                    $order = 'DESC';
                }
                $sort = substr($sort, 1);
            }

            if (!$this->validateField($sort)) {
                throw new \Exception("$sort is not a valid field");
            }

            print("\n $sort and $order ");





/*
            $qb
                ->select('*') //TODO change this
                ->from('archive_collection')
                ->orderBy('

 */

        }




        $sql = 'select * from archive_collection limit 5';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $response->withJson($result);
    }

    private function validateField($field)
    {
        $dbFields = 
            ['id', 'name', 'year_min', 'year_max', 'item_count', 'is_published', 'created', 'modified', 'donor_id'];
        return in_array($field, $dbFields);
    }

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
    }
}
