<?php

namespace Kronofoto;

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
    private $fields;
    private $container;

    public function __construct(array $params, array $fields, ContainerInterface $container)
    {
        $this->params = $params;
        $this->fields = $fields;
        $this->container = $container;
    }

    /*
    public function hasFilterParam()
    {
        return array_key_exists(self::FILTER_KEY, $this->params);
    }

    public function getFilterParams()
    {
        //get the filter subarray
        $filterArray = $this->params[self::FILTER_KEY];
        //costruct the parameters
        $filterParams = array();
        foreach ($filterArray as $key=>$val) {
            $this->validateField($key);
            $this->validateValue($key, $val);
            $filter = array();
            $filter['field'] = $key;
            $filter['operator'] = $this->getFilterOperator($key);
            $filter['value'] = $val;
            $filterParams[] = $filter;
        }
        return $filterParams;
    }
     */

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
        $this->validateField($sort);
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


    //TODO refactor of course!!
    private function validateField($field)
    {
        if (!in_array($field, $this->fields)) {
            throw new \Exception("$field is not a valid field name");
        }
    }
/*
    private function validateValue($key, $value)
    {
        if (empty($value)) {
            throw new \Exception("Value for $key cannot be empty");
        }
    }

    private function getFilterOperator($key)
    {
        //if the key ends with FILTER_SUFFIX_MIN, the operator is <=
        $lenMin = strlen(self::FILTER_SUFFIX_MIN);
        if (strlen($key) > $lenMin) {
            if (substr_compare($key, self::FILTER_SUFFIX_MIN, -$lenMin) === 0) {
                return '<=';
            }
        }
        //if the key ends with FILTER_SUFFIX_MAX, the operator is >=
        $lenMax = strlen(self::FILTER_SUFFIX_MAX);
        if (strlen($key) > $lenMax) {
            if (substr_compare($key, self::FILTER_SUFFIX_MAX, -$lenMax) === 0) {
                //the key ends with FILTER_SUFFIX_MIN
                return '>=';
            }
        }
        //in all other cases, the operator is =
        return '=';
    }
 */
}
