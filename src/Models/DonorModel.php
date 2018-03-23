<?php
namespace Kronofoto\Models;

class DonorModel extends Model
{
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
