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

    public function test42() 
    {
        return 41;
    }

    public function read(Request $request, Response $response, array $args) 
    {
    }

    public function validateField($field)
    {
        $dbFields = 
            ['id', 'name', 'year_min', 'year_max', 'item_count', 'is_published', 'created', 'modified', 'donor_id', 'featured_item_id'];
        if (!in_array($field, $dbFields)) {
            throw new \Exception("$field is not a valid field name");
        }
    }

    public function getCollections(Request $request, Response $response, array $args) 
    {
        //TODO: change this (default paging should come from the config)
        $offset = 0; //default
        $limit = 100; //default

        $conn = $this->container->db;

        $qBuilder = $conn->createQueryBuilder();

        $qParams = $request->getQueryParams();

        $qBuilder
            ->select(
                'c.id', 
                'c.name', 
                'c.year_min', 
                'c.year_max', 
                'c.item_count', 
                'c.is_published',
                'c.created',
                'c.modified',
                'c.featured_item_id',
                'c.donor_id',
                'u.first_name as donor_first_name',
                'u.last_name as donor_last_name')
                ->from('archive_collection', 'c')
                ->innerJoin('c', 'accounts_user', 'u', 'c.donor_id = u.id')
                ->where('is_published = 1'); //because for now this is for public site only


        //TODO process filtering
        if (array_key_exists('filter', $qParams)) {
            //get filtering params
            $filterParams = $qParams['filter'];

            //now validate
            foreach ($filterParams as $field=>$val) {
                $this->validateField($field);
            } 


            if (array_key_exists('year_min', $filterParams)) {
                $yearMin = $filterParams['year_min'];
                $qBuilder
                    ->andWhere('year_min >= :yearMin')
                    ->setParameter('yearMin', $yearMin);
            }

            if (array_key_exists('year_max', $filterParams)) {
                $yearMax = $filterParams['year_max'];
                $qBuilder
                    ->andWhere('year_max <= :yearMax')
                    ->setParameter('yearMax', $yearMax);
            }
        }

        //TODO process sorting
        if (array_key_exists('sort', $qParams)) {
            $sort = $qParams['sort'];

            $order = 'ASC';
            if ($sort[0] === '-') {
                $order = 'DESC';
                $sort = substr($sort, 1);
            }

            $this->validateField($sort);


            $qBuilder
                ->orderBy($sort, $order);

        }

        //now process paging TODO: ADD VALIDATION!
        if (array_key_exists('limit', $qParams)) {
            $limit = (int)$qParams['limit'];
        }
        if (array_key_exists('offset', $qParams)) {
            $offset = (int)$qParams['offset'];
        }


        $qBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $stmt = $qBuilder->execute();
        $result = $stmt->fetchAll();

        return $response->withJson($result);
    }

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
    }
}
