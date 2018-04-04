<?php
namespace Kronofoto\Models;

class CollectionModel extends Model
{
    protected function getSortCriteria() 
    {
        return [
            'id', 
            'name',
            'year_min',
            'year_max',
            'item_count',
            'is_published',
            'created',
            'modified',
            'donor_id',
            'donor_first_name',
            'donor_last_name'
        ];
    }

    protected function getFilterCriteria() 
    {
        return [];
    }
}

