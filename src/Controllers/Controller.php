<?php
namespace Kronofoto\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

use Kronofoto\Pagination;
use Kronofoto\HttpHelper;
use Kronofoto\QueryStringHelper;

abstract class Controller
{
    protected $container;
    protected $model;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->model = $this->getModel();
    }

    public function read($request, $response, $args) 
    {
        $qBuilder = $this->getQueryBuilder();
        $this->selectOneRecord($qBuilder, $args);
        $stmt = $qBuilder->execute();
        $result = $stmt->fetch();

        if ($result) {
            return $response->withJson($result);
        }
        else {
            $record = $this->model->getName();
            $error = array(
                'error' =>
                array(
                    'status' => '404',
                    'message' => "Requested $record not found",
                    'detail' => 'Invalid id'
                )
            );
            return $response->withJson($error, 404);
        }
    }

    protected function getRecords($request, $response, $args, $select, $paging=True) 
    {
        $qBuilder = $this->getQueryBuilder();
        $qParams = $request->getQueryParams();
        $qs = new QueryStringHelper($qParams, $this->model, $this->container);

        //add select
        $select($qBuilder);

        //add filters
        if ($qs->hasFilterParam()) {
            $this->addFilterParams($qBuilder, $qs); 
        }

        //add sort
        if ($qs->hasSortParam()) {
            $qBuilder
                ->orderBy($qs->getSortField(), $qs->getSortOrder());
        }
        else {
            $qBuilder
                ->orderBy($qs->getDefaultSortField(), $qs->getDefaultSortOrder());
        }


        //add paging
        if ($paging) {
            $limit = $qs->getLimit();
            $offset = $qs->getOffset();
            $qBuilder
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        //execute
        $stmt = $qBuilder->execute();
        $result = $stmt->fetchAll();

        if ($paging) {
            $response = $this->setPagingHeaders($response, $limit, $offset, $qs);
        }
        return $response->withJson($result);
    }

    protected function setPagingHeaders($response, $limit, $offset, $queryStringHelper) 
    {
        $totalRecords = $this->getRecordsCount($queryStringHelper);
        $pager = new Pagination($offset, $limit, $totalRecords);
        $response = $response->withHeader(HttpHelper::PAGINATION_TOTAL_RECORDS, $totalRecords);
        $response = $response->withHeader(HttpHelper::PAGINATION_PAGE_SIZE, $pager->pageSize());
        $response = $response->withHeader(HttpHelper::PAGINATION_TOTAL_PAGES, $pager->totalPages());
        $response = $response->withHeader(HttpHelper::PAGINATION_FIRST_RECORD, $pager->firstRecord());
        $response = $response->withHeader(HttpHelper::PAGINATION_LAST_RECORD, $pager->lastRecord());
        $response = $response->withHeader(HttpHelper::PAGINATION_CURRENT_PAGE_NUMBER, $pager->currentPageNumber());
        $response = $response->withHeader(HttpHelper::PAGINATION_CURRENT_PAGE_SIZE, $pager->currentPageSize());
        return $response;
    }

    protected function getQueryBuilder()
    {
        return $this->container->db->createQueryBuilder();
    }

    private function getRecordsCount($queryStringHelper)
    {
        $qBuilder = $this->getQueryBuilder();
        $this->selectCount($qBuilder);

        if ($queryStringHelper->hasFilterParam()) {
            $this->addFilterParams($qBuilder, $queryStringHelper); 
        }
        $stmt = $qBuilder->execute();
        return $stmt->fetchColumn(0);
    }

    protected abstract function getModel();

    protected abstract function selectOneRecord($queryBuilder, $args);

    protected abstract function selectCount($queryBuilder);

    protected abstract function addFilterParams($queryBuilder, $queryStringHelper);
}
