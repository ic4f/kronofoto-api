<?php
namespace Kronofoto\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Kronofoto\Models\CollectionModel;
use Kronofoto\QueryStringHelper;
use Kronofoto\Pagination;
use Kronofoto\HttpHelper;

class CollectionController extends Controller
{ 
    public function getCollections($request, $response, $args) 
    {
        $select = function($qBuilder) {
            $qBuilder
                ->select(
                    'c.id', 
                    'c.name', 
                    'c.year_min as yearMin', 
                    'c.year_max as yearMax', 
                    'c.item_count as itemCount', 
                    'c.created',
                    'c.modified',
                    'i.identifier as featuredItemIdentifier',
                    'c.donor_id as donorId',
                    'u.first_name as donorFirstName',
                    'u.last_name as donorLastName'
                )
                ->from('archive_collection', 'c')
                ->innerJoin('c', 'accounts_user', 'u', 'c.donor_id = u.id')
                ->innerJoin('c', 'archive_item', 'i', 'c.featured_item_id = i.id')
                ->where('c.is_published = 1'); //because for now this is for public site only
        };
        return $this->getRecords($request, $response, $args, $select);
    }
    
    protected function getModel()
    {
        return $this->container->CollectionModel;
    }

    protected function selectOneRecord($queryBuilder, $args)
    { 
        $id = $args['id'];

        $queryBuilder
            ->select(
                'c.id', 
                'c.name', 
                'c.year_min as yearMin', 
                'c.year_max as yearMax', 
                'c.item_count as itemCount', 
                'c.is_published as isPublished',
                'c.description',
                'c.created',
                'c.modified',
                'c.featured_item_id as fieaturedItemId',
                'i.identifier as featuredItemIdentifier',
                'c.donor_id as donorId',
                'u.first_name as donorFirstName',
                'u.last_name as donorLastName'
            )
            ->from('archive_collection', 'c')
            ->innerJoin('c', 'accounts_user', 'u', 'c.donor_id = u.id')
            ->innerJoin('c', 'archive_item', 'i', 'c.featured_item_id = i.id')
            ->where('c.id = :id')
            ->setParameter('id', $id);
    }

    protected function addFilterParams($queryBuilder, $queryStringHelper)
    {
        //TODO
    }

    protected function selectCount($queryBuilder)
    {
        $queryBuilder->select('count(*)')
            ->from('archive_collection', 'c')
            //no joins for counting; but this could cause a bug (null related record)
            ->where('c.is_published = 1'); //because for now this is for public site only
    }

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
        //TODO
    }
}
