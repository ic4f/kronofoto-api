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
        $id = $args['id'];

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
            ->where('i.id = :id')
            ->setParameter('id', $id);
    }

    protected function addFilterParams($queryBuilder, $queryStringHelper)
    {
        $filterParams = $queryStringHelper->getFilterParams();
        foreach ($filterParams as $fp) {
            $key= $fp['key'];
            $value = $fp['value'];

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
        echo 'collection items';
    }
}
