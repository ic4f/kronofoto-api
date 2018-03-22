<?php
namespace Kronofoto\Models;

class ItemModel extends Model
{
    public function validateSort($criteria)
    {
        if (!in_array($criteria, $this->getSortCriteria())) {
            throw new \Exception('Invalid sort criteria');
        }
        return true;
    }

    public function validateFilter($criteria)
    {
        if (!in_array($criteria, $this->getFilterCriteria())) {
            throw new \Exception('Invalid filter criteria');
        }
        return true;
    }

    protected function getSortCriteria() 
    {
        return [
            'id',
            'identifier',
            'collection_id',
            'latitude',
            'longitude',
            'year_min',
            'year_max',
            'created',
            'modified'
        ];
    }

    protected function getFilterCriteria() 
    {
        return [
            'identifier',
            'year',
            'before',
            'after'
        ];
    }
}

