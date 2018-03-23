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
            'featured_item_id'
        ];
    }

    protected function getFilterCriteria() 
    {
        return [];
    }
}

