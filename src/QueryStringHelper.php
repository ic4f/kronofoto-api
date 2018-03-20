<?php

namespace Kronofoto;

use Interop\Container\ContainerInterface;

class QueryStringHelper
{
    public const OFFSET_KEY = 'offset';
    public const LIMIT_KEY = 'limit';
    public const SORT_KEY = 'sort';
    public const SORT_ORDER_DEFAULT = 'ASC';
    public const SORT_ORDER_DESCENDING = 'DESC';
    public const FILTER_KEY = 'filter';

    private $params;
    private $container;

    public function __construct(array $params, ContainerInterface $container)
    {
        $this->params = $params;
        $this->container = $container;
    }

    public function getOffset()
    {
        if (array_key_exists(self::OFFSET_KEY, $this->params)) {
            return (int)$this->params[self::OFFSET_KEY];
        }
        return 0;
    }

    public function getLimit()
    {
        if (array_key_exists(self::LIMIT_KEY, $this->params)) {
            $limit = (int)$this->params[self::LIMIT_KEY];
            $maxRecords = (int)$this->container['settings']['paging']['max_records'];
            return min($limit, $maxRecords);
        }
        return (int)$this->container['settings']['paging']['default_page_size'];
    }

    public function hasSortParam()
    {
        return array_key_exists(self::SORT_KEY, $this->params);
    }

    public function getSortField()
    {
        $sort = $this->params[self::SORT_KEY];

        if ($sort[0] === '-') {
            $sort = substr($sort, 1);
        }
        $this->validateField($sort);
        return $sort;
    }

    public function getSortOrder()
    {
        $sort = $this->params[self::SORT_KEY];
        return ($sort[0] === '-') ? self::SORT_ORDER_DESCENDING : self::SORT_ORDER_DEFAULT;
    }

    //TODO refactor of course
    private function validateField($field)
    {
        $dbFields = [
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
        if (!in_array($field, $dbFields)) {
            throw new \Exception("$field is not a valid field name");
        }
    }
}
