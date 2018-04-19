<?php
namespace Kronofoto\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Kronofoto\Models\PageModel;

class PageController extends Controller
{
    protected function selectOneRecord($queryBuilder, $args)
    { 
        $slug = $args['slug'];

        $queryBuilder
            ->select(
                'id',
                'slug',
                'title',
                'body',
                'created',
                'modified'
            )
            ->from('archive_page')
            ->where('slug = :slug')
            ->setParameter('slug', $slug);
    }

    protected function getModel()
    {
        return $this->container->PageModel;
    }

    protected function selectCount($queryBuilder) {}

    protected function addFilterParams($queryBuilder, $queryStringHelper) {}
}
