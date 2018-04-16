<?php
namespace Kronofoto\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

use Kronofoto\Models\DonorModel;
use Kronofoto\QueryStringHelper;

class DonorController
{
    private $container;
    private $model;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->model = $this->container->DonorModel;
    }

    public function read(Request $request, Response $response, array $args) 
    {
        $id = $args['id'];

        $conn = $this->container->db;

        $qBuilder = $conn->createQueryBuilder();

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
            ->where('u.id = :id')
            ->setParameter('id', $id);

        $stmt = $qBuilder->execute();
        $result = $stmt->fetch();

        if (!$result) {
            $error = array(
                'error' =>
                array(
                    'status' => '404',
                    'message' => 'Requested donor not found',
                    'detail' => 'Invalid id'
                )
            );
            return $response->withJson($error, 404);
        }
        else {
            return $response->withJson($result);
        }
    }

    public function getDonors(Request $request, Response $response, array $args) 
    {
        $conn = $this->container->db;

        $qBuilder = $conn->createQueryBuilder();

        $qParams = $request->getQueryParams();

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

        $qs = new QueryStringHelper($qParams, $this->model, $this->container);


        //TODO this will need refactoring
        if ($qs->hasFilterParam()) {
            $filterParams = $qs->getFilterParams();
            foreach ($filterParams as $fp) {
                $field = $fp['key'];
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
