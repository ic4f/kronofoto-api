<?php
namespace Kronofoto;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class DonorController
{
    const FIELDS = [
        'user_id', 
        'first_name', 
        'last_name', 
        'collection_count', 
        'item_count', 
        'created', 
        'modified' 
    ];

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function read(Request $request, Response $response, array $args) 
    {
        echo 'donor';
    }

    public function getDonors(Request $request, Response $response, array $args) 
    {
        $conn = $this->container->db;

        $qBuilder = $conn->createQueryBuilder();

        $qParams = $request->getQueryParams();

        $qBuilder
            ->select(
                'd.user_id',
                'u.first_name',
                'u.last_name',
                'd.collection_count',
                'd.item_count',
                'd.created',
                'd.modified'
            )
            ->from('archive_donor', 'd')
            ->innerJoin('d', 'accounts_user', 'u', 'd.user_id = u.id')
            ->where('1 = 1'); 

        $qs = new QueryStringHelper($qParams, self::FIELDS, $this->container);

        //TODO this will need refactoring
        if ($qs->hasFilterParam()) {
            $filterParams = $qs->getFilterParams();
            foreach ($filterParams as $fp) {
                $field = $fp['field'];
                $value = $fp['value'];
                $value .= '%';
                $qBuilder
                    ->andWhere(
                        $qBuilder->expr()->like($field, ":$field"))
                    ->setParameter($field, "$value");
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
}
