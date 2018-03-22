<?php
namespace Kronofoto\Models;

class CollectionModel extends Model
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

