<?php
namespace Kronofoto\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

use Kronofoto\Models\ItemModel;
use Kronofoto\QueryStringHelper;

class ItemController 
{
    private $container;
    private $model;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->model = $this->container->ItemModel;
    }

    public function read(Request $request, Response $response, array $args) 
    {
        $id = $args['id'];

        $conn = $this->container->db;

        $qBuilder = $conn->createQueryBuilder();

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
            ->where('i.id = :id')
            ->setParameter('id', $id);

        $stmt = $qBuilder->execute();
        $result = $stmt->fetch();

        if (!$result) {
            $error = array(
                'error' =>
                array(
                    'status' => '404',
                    'message' => 'Requested item not found',
                    'detail' => 'Invalid id'
                )
            );
            return $response->withJson($error, 404);
        }
        else {
            return $response->withJson($result);
        }
    }

    public function getItems(Request $request, Response $response, array $args) 
    {
        $conn = $this->container->db;

        $qBuilder = $conn->createQueryBuilder();

        $qParams = $request->getQueryParams();

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

        $qs = new QueryStringHelper($qParams, $this->model, $this->container);
        //
        //TODO this will need refactoring
        if ($qs->hasFilterParam()) {
            $filterParams = $qs->getFilterParams();
            foreach ($filterParams as $fp) {
                $key= $fp['key'];
                $value = $fp['value'];

                if ($key == 'identifier') {
                    $value .= '%';
                    $qBuilder
                        ->andWhere(
                            $qBuilder->expr()->like($key, ":$key"))
                            ->setParameter($key, "$value");
                }
 
                if ($key == 'year' || $key == 'before') {
                    $qBuilder
                        ->andWhere(
                            $qBuilder->expr()->lte('year_min', ':year_min'))
                            ->setParameter('year_min', "$value");
                }
                
                if ($key == 'year' || $key == 'after') {
                    $qBuilder
                        ->andWhere(
                            $qBuilder->expr()->gte('year_max', ':year_max'))
                            ->setParameter('year_max', "$value");
                }
           }
        }

        if ($qs->hasSortParam()) {
            $qBuilder
                ->orderBy($qs->getSortField(), $qs->getSortOrder());
        }

        $qBuilder
            ->setFirstResult($qs->getOffset())
            ->setMaxResults($qs->getLimit());

        $stmt = $qBuilder->execute();

        $result = $stmt->fetchAll();

        return $response->withJson($result);
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
