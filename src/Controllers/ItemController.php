<?php
namespace Kronofoto\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Kronofoto\Models\ItemModel;
use Kronofoto\QueryStringHelper;
use Kronofoto\Pagination;
use Kronofoto\HttpHelper;

class ItemController extends Controller
{

    public function getRandomFeaturedItem($request, $response, $args) 
    {
        $qBuilder = $this->getQueryBuilder();

        $qBuilder
            ->select(
                'id',
                'identifier'
            )
            ->from('archive_item')
            //TODO: add is_featured after it's implemented (item field? metadata field?)
            ->where('is_published = 1')
            ->orderBy('RAND()')
            ->setMaxResults(1);

        //execute
        $stmt = $qBuilder->execute();
        $result = $stmt->fetch();
        return $response->withJson($result);
    }


    //TODO consider factoring out into separate controller
    public function getItemMetadata($request, $response, $args) 
    {
        $identifier = $args['identifier'];

        $qBuilder = $this->getQueryBuilder();

        $qBuilder
            ->select(
                'm.value',
                'm.element_id as elementId',
                'e.name as element'
            )
            ->from('archive_itemmetadata', 'm')
            ->innerJoin('m', 'archive_metadataelement', 'e', 'm.element_id = e.id')
            ->innerJoin('m', 'archive_item', 'i', 'm.item_id = i.id')
            ->where('i.identifier = :identifier')
            ->setParameter('identifier', $identifier);


        //execute
        $stmt = $qBuilder->execute();
        $result = $stmt->fetchAll();
        return $response->withJson($result);
    }

    public function getItems($request, $response, $args) 
    {
        $select = function($qBuilder) { 
            $qBuilder
                ->select(
                    'i.id',
                    'i.identifier',
                    'i.collection_id as collectionId',
                    'i.latitude',
                    'i.longitude',
                    'i.year_min as yearMin',
                    'i.year_max as yearMax',
                    'i.is_published as isPublished',
                    'i.created',
                    'i.modified'
                )
                ->from('archive_item', 'i')
                ->where('i.is_published = 1'); 
        };
        return $this->getRecords($request, $response, $args, $select);
    }

    protected function getModel()
    {
        return $this->container->ItemModel;
    }

    protected function selectOneRecord($queryBuilder, $args)
    { 
        $identifier = $args['identifier']; //not id

        $queryBuilder
            ->select(
                'i.id',
                'i.identifier',
                'i.collection_id as collectionId',
                'i.latitude',
                'i.longitude',
                'i.year_min as yearMin',
                'i.year_max as yearMax',
                'i.is_published as isPublished',
                'i.created',
                'i.modified'
            )
            ->from('archive_item', 'i')
            ->where('i.identifier = :identifier')
            ->setParameter('identifier', $identifier);
    }

    protected function addFilterParams($queryBuilder, $queryStringHelper)
    {
        $filterParams = $queryStringHelper->getFilterParams();
        foreach ($filterParams as $fp) {
            $key= $fp['key'];
            $value = $fp['value'];

            if ($key == 'collection') {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->eq('collection_id', ':collection_id'))
                        ->setParameter('collection_id', "$value");
            }

            if ($key == 'identifier') {
                $value .= '%';
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->like($key, ":$key"))
                        ->setParameter($key, "$value");
            }

            if ($key == 'year' || $key == 'before') {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->lte('year_min', ':year_min'))
                        ->setParameter('year_min', "$value");
            }

            if ($key == 'year' || $key == 'after') {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->gte('year_max', ':year_max'))
                        ->setParameter('year_max', "$value");
            }
        }
    }

    protected function selectCount($queryBuilder)
    { 
        $queryBuilder->select('count(*)')
            ->from('archive_item')
            //no joins for counting; but this could cause a bug (null related record)
            ->where('is_published = 1'); //because for now this is for public site only
    }

    public function getDonorItems(Request $request, Response $response, array $args)
    {
        echo 'donor items';
    }

    public function getCollectionItems(Request $request, Response $response, array $args) 
    {
        //implement this now!
        echo 'collection items';
    }
}
