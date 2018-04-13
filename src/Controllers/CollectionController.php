<?php
namespace Kronofoto\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

use Kronofoto\Models\CollectionModel;
use Kronofoto\QueryStringHelper;
use Kronofoto\Pagination;
use Kronofoto\HttpHelper;

class CollectionController 
{ 
    private $container;
    private $model;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->model = $this->container->CollectionModel;
    }

    public function read(Request $request, Response $response, array $args) 
    {
        $id = $args['id'];

        $conn = $this->container->db;

        $qBuilder = $conn->createQueryBuilder();

        $qBuilder
            ->select(
                'c.id', 
                'c.name', 
                'c.year_min', 
                'c.year_max', 
                'c.item_count', 
                'c.is_published',
                'c.description',
                'c.created',
                'c.modified',
                'c.featured_item_id',
                'i.identifier as featured_item_identifier',
                'c.donor_id',
                'u.first_name as donor_first_name',
                'u.last_name as donor_last_name'
            )
            ->from('archive_collection', 'c')
            ->innerJoin('c', 'accounts_user', 'u', 'c.donor_id = u.id')
            ->innerJoin('c', 'archive_item', 'i', 'c.featured_item_id = i.id')
            ->where('c.id = :id')
            ->setParameter('id', $id);

        $stmt = $qBuilder->execute();
        $result = $stmt->fetch();

        if (!$result) {
            $error = array(
                'error' =>
                array(
                    'status' => '404',
                    'message' => 'Requested collection not found',
                    'detail' => 'Invalid id'
                )
            );
            return $response->withJson($error, 404);
        }
        else {
            return $response->withJson($result);
        }
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
                'c.created',
                'c.modified',
                'i.identifier as featured_item_identifier',
                'c.donor_id',
                'u.first_name as donor_first_name',
                'u.last_name as donor_last_name'
            )
            ->from('archive_collection', 'c')
            ->innerJoin('c', 'accounts_user', 'u', 'c.donor_id = u.id')
            ->innerJoin('c', 'archive_item', 'i', 'c.featured_item_id = i.id')
            ->where('c.is_published = 1'); //because for now this is for public site only

        $qs = new QueryStringHelper($qParams, $this->model, $this->container);

        if ($qs->hasSortParam()) {
            $qBuilder
                ->orderBy($qs->getSortField(), $qs->getSortOrder());
        }

        $limit = $qs->getLimit();
        $offset = $qs->getOffset();

        $qBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $stmt = $qBuilder->execute();
        $result = $stmt->fetchAll();

        $response = $this->setPagingHeaders($response, $limit, $offset);

        return $response->withJson($result);
    }

    private function setPagingHeaders($response, $limit, $offset) 
    {
        $totalRecords = $this->getCollectionsCount();

        $pager = new Pagination($offset, $limit, $totalRecords);

        $response = $response->withHeader(HttpHelper::PAGINATION_TOTAL_RECORDS, $totalRecords);
        $response = $response->withHeader(HttpHelper::PAGINATION_FIRST_RECORD, $pager->firstRecord());
        $response = $response->withHeader(HttpHelper::PAGINATION_LAST_RECORD, $pager->lastRecord());
        $response = $response->withHeader(HttpHelper::PAGINATION_TOTAL_PAGES, $pager->totalPages());
        $response = $response->withHeader(HttpHelper::PAGINATION_PAGE_SIZE, $pager->currentPageSize());
        $response = $response->withHeader(HttpHelper::PAGINATION_PAGE_NUMBER, $pager->currentPageNumber());

        return $response;

    }

    private function getCollectionsCount()
    {
        $conn = $this->container->db;

        $qBuilder = $conn->createQueryBuilder();

        //$qParams = $request->getQueryParams(); TODO add filtering params

        $qBuilder->select('count(*)')
            ->from('archive_collection', 'c')
            //no joins for counting; but this could cause a bug (null related record)
            ->where('c.is_published = 1'); //because for now this is for public site only
        //also, must add the same filtering!
        
        $stmt = $qBuilder->execute();
        return $stmt->fetchColumn(0);
    }

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
        //TODO
    }
}
