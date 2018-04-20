<?php
namespace Kronofoto\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Kronofoto\Models\DonorModel;
use Kronofoto\QueryStringHelper;
use Kronofoto\Pagination;
use Kronofoto\HttpHelper;

class DonorController extends Controller
{
    public function getAllDonors($request, $response, $args) 
    {
        $select = function($qBuilder) {
            $qBuilder
                ->select(
                    'd.user_id as userId',
                    'u.first_name as firstName',
                    'u.last_name as lastName',
                    'd.collection_count as collectionCount',
                    'd.item_count as itemCount',
                    'd.created',
                    'd.modified'
                )
                ->from('archive_donor', 'd')
                ->innerJoin('d', 'accounts_user', 'u', 'd.user_id = u.id')
                ->where('1 = 1'); 
        };
        //do not include paging
        return $this->getRecords($request, $response, $args, $select, False);
    }

    public function getDonors($request, $response, $args) 
    {
        $select = function($qBuilder) {
            $qBuilder
                ->select(
                    'd.user_id as userId',
                    'u.first_name as firstName',
                    'u.last_name as lastName',
                    'd.collection_count as collectionCount',
                    'd.item_count as itemCount',
                    'd.created',
                    'd.modified'
                )
                ->from('archive_donor', 'd')
                ->innerJoin('d', 'accounts_user', 'u', 'd.user_id = u.id')
                ->where('1 = 1'); 
        };
        return $this->getRecords($request, $response, $args, $select);
    }

    protected function getModel()
    {
        return $this->container->DonorModel;
    }

    protected function selectOneRecord($queryBuilder, $args)
    { 
        $id = $args['id'];

        $queryBuilder
            ->select(
                'd.user_id as userId',
                'u.first_name as firstName',
                'u.last_name as lastName',
                'd.collection_count as collectionCount',
                'd.item_count as itemCount',
                'd.created',
                'd.modified'
            )
            ->from('archive_donor', 'd')
            ->innerJoin('d', 'accounts_user', 'u', 'd.user_id = u.id')
            ->where('u.id = :id')
            ->setParameter('id', $id);
    }

    protected function selectCount($queryBuilder)
    {
        $queryBuilder->select('count(*)')
            ->from('archive_donor', 'd')
            //a join to accommodate filtering on the user table
                ->innerJoin('d', 'accounts_user', 'u', 'd.user_id = u.id')
                ->where('1 = 1'); 
    }

    protected function addFilterParams($queryBuilder, $queryStringHelper)
    {
        $filterParams = $queryStringHelper->getFilterParams();
        foreach ($filterParams as $fp) {
            $field = $fp['key'];
            $value = $fp['value'];
            $value .= '%';
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->like($field, ":$field"))
                    ->setParameter($field, "$value");
        }
    }
}
