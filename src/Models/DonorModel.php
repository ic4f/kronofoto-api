<?php
namespace Kronofoto\Models;

class DonorModel extends Model
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
            'user_id', 
            'first_name',
            'last_name',
            'collection_count',
            'item_count',
            'created',
            'modified'
        ];
    }

    protected function getFilterCriteria() 
    {
        return [
            'first_name',
            'last_name'
        ];
    }
}
