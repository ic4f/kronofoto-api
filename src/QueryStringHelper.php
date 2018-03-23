<?php

namespace Kronofoto;

use Kronofoto\Models\Model;

use Interop\Container\ContainerInterface;

class QueryStringHelper
{
    const OFFSET_KEY = 'offset';
    const LIMIT_KEY = 'limit';
    const SORT_KEY = 'sort';
    const SORT_ORDER_DEFAULT = 'ASC';
    const SORT_ORDER_DESCENDING = 'DESC';
    const FILTER_KEY = 'filter';
    const FILTER_SUFFIX_MIN = '_min';
    const FILTER_SUFFIX_MAX = '_max';

    private $params;
    private $model;
    private $container;

    public function __construct(array $params, Model $model, ContainerInterface $container)
    {
        $this->params = $params;
        $this->model = $model;
        $this->container = $container;
    }

    public function hasFilterParam()
    {
        return array_key_exists(self::FILTER_KEY, $this->params);
    }

    public function getFilterParams()
    {
        $filterArray = $this->params[self::FILTER_KEY];
        $filterParams = array();
        foreach ($filterArray as $key=>$val) {
            $this->model->validateFilter($key);
            $this->validateValue($key, $val);
            $filter = array();
            $filter['key'] = $key;
            //TODO operator can vary...
            $filter['operator'] = '=';
            $filter['value'] = $val;
            $filterParams[] = $filter;
        }
        return $filterParams;
    }

    public function hasSortParam()
    {
        return array_key_exists(self::SORT_KEY, $this->params);
    }

    public function getSortField()
    {
        $sort = $this->params[self::SORT_KEY];

        if (empty($sort)) {
            throw new \Exception("Sort field cannot be empty");
        }

        if ($sort[0] === '-') {
            $sort = substr($sort, 1);
        }
        $this->model->validateSort($sort);
        return $sort;
    }

    public function getSortOrder()
    {
        $sort = $this->params[self::SORT_KEY];
        return ($sort[0] === '-') ? self::SORT_ORDER_DESCENDING : self::SORT_ORDER_DEFAULT;
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

    private function validateValue($key, $value)
    {
        if (empty($value)) {
            throw new \Exception("Value for $key cannot be empty");
        }
    }
}
