<?php
namespace Kronofoto;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class CollectionController 
{ 
    const FIELDS = [
        'id', 
        'name', 
        'year_min', 
        'year_max', 
        'item_count', 
        'is_published', 
        'created', 
        'modified', 
        'donor_id', 
        'featured_item_id'
    ];

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function read(Request $request, Response $response, array $args) 
    {
        //TODO
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
                'u.last_name as donor_last_name'
            )
            ->from('archive_collection', 'c')
            ->innerJoin('c', 'accounts_user', 'u', 'c.donor_id = u.id')
            ->where('is_published = 1'); //because for now this is for public site only

        $qs = new QueryStringHelper($qParams, self::FIELDS, $this->container);

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

    public function getDonorCollections(Request $request, Response $response, array $args) 
    {
        //TODO
    }
}
