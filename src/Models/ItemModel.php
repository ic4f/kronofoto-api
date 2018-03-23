<?php
namespace Kronofoto\Models;

class ItemModel extends Model
{
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

