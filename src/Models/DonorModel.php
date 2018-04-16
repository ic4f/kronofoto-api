<?php
namespace Kronofoto\Models;

class DonorModel extends Model
{
    protected function getSortCriteria() 
    {
        return [
            'userId', 
            'firstName',
            'lastName',
            'collectionCount',
            'itemCount',
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
