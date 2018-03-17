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


        //TODO refactor all this

        if (array_key_exists('year_min', $qParams)) {
            $yearMin = $qParams['year_min'];
            $qBuilder
                ->andWhere('year_min >= :yearMin')
                ->setParameter('yearMin', $yearMin);
        }
        
        if (array_key_exists('year_max', $qParams)) {
            $yearMax = $qParams['year_max'];
            $qBuilder
                ->andWhere('year_max <= :yearMax')
                ->setParameter('yearMax', $yearMax);
        }

        if (array_key_exists('sort', $qParams)) {
            $sort = $qParams['sort'];

            $order = 'ASC';
            if ($sort[0] === '-') {
                $order = 'DESC';
                $sort = substr($sort, 1);
            }

            if (!$this->validateField($sort)) {
                throw new \Exception("$sort is not a valid field");
            }


            $qBuilder
                ->orderBy($sort, $order);



        }
        $stmt = $qBuilder->execute();
        $result = $stmt->fetchAll();

        return $response->withJson($result);
    }

    private function validateField($field)
    {
        $dbFields = 
            ['id', 'name', 'year_min', 'year_max', 'item_count', 'is_published', 'created', 'modified', 'donor_id', 'featured_item_id'];
        return in_array($field, $dbFields);
    }

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
    }
}
