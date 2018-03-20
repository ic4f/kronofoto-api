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




        $qs = new QueryStringHelper($qParams, $this->container);

        if ($qs->hasSortParam()) {
            $qBuilder
                ->orderBy($qs->getSortField(), $qs->getSortOrder());
        }

        //$offset = 0; //default
        //$limit = $this->container['settings']['paging']['max_records'];

        $qBuilder
            ->setFirstResult($qs->getOffset())
            ->setMaxResults($qs->getLimit());


        //        //TODO process filtering
        //        if (array_key_exists('filter', $qParams)) {
        //            //get filtering params
        //            $filterParams = $qParams['filter'];
        //
        //            //now validate
        //            foreach ($filterParams as $field=>$val) {
        //                $this->validateField($field);
        //            } 
        //
        //
        //            if (array_key_exists('year_min', $filterParams)) {
        //                $yearMin = $filterParams['year_min'];
        //                $qBuilder
        //                    ->andWhere('year_min >= :yearMin')
        //                    ->setParameter('yearMin', $yearMin);
        //            }
        //
        //            if (array_key_exists('year_max', $filterParams)) {
        //                $yearMax = $filterParams['year_max'];
        //                $qBuilder
        //                    ->andWhere('year_max <= :yearMax')
        //                    ->setParameter('yearMax', $yearMax);
        //            }
        //        }
        //
        //
        //        $ch = new SomeHelper($qParams); //takes the current param array at construction
        //
        //
        //        if ($ch.hasFilter()) {
        //            foreach ($filterObjects as $filter) {
        //                $qBuilder
        //                    ->andWhere($filter->expression)
        //                    ->setParameter($filter->column, $filter->value);
        //            }
        //        }
        //
        //
        //

        //        //now process paging TODO: ADD VALIDATION!
        //        if (array_key_exists('limit', $qParams)) {
        //            $limit = (int)$qParams['limit'];
        //        }
        //        if (array_key_exists('offset', $qParams)) {
        //            $offset = (int)$qParams['offset'];
        //        }
        //
        //
        //$qBuilder
            //            ->setFirstResult($offset)
        //
            //
        $stmt = $qBuilder->execute();
        $result = $stmt->fetchAll();

        return $response->withJson($result);
    }

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
    }
}
