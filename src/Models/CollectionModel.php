<?php
namespace Kronofoto\Models;

class CollectionModel extends Model
{
    public function getName() { return 'collection'; }
    
    protected function getSortCriteria() 
    {
        return [
            'id', 
            'name',
            'yearMin',
            'yearMax',
            'itemCount',
            'isPublished',
            'created',
            'modified',
            'donorId',
            'donorFirstName',
            'donorLastName'
        ];
    }

    protected function getFilterCriteria() 
    {
        return [];
    }

    public function getDefaultSortField()
    {
        return 'donorLastName';
    }

    public function getDefaultSortOrder()
    {
        return 'asc';
    }
}
