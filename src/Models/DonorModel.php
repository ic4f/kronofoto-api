<?php
namespace Kronofoto\Models;

class DonorModel extends Model
{
    public function getName() { return 'donor'; }

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

    public function getDefaultSortField()
    {
        return 'lastName';
    }

    public function getDefaultSortOrder()
    {
        return 'asc';
    }
}
